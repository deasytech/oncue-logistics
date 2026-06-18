<?php

namespace App\Livewire;

use App\Models\PaymentReceipt;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PaymentReceipts extends Component
{
  use WithFileUploads, WithPagination;

  public $receiptFile;
  public $description = '';
  public $confirmingDelete = false;
  public $receiptToDelete = null;
  public $viewingReceipt = null;
  public $showViewModal = false;

  protected $queryString = [];

  protected $rules = [
    'receiptFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
    'description' => 'nullable|string|max:500',
  ];

  protected $messages = [
    'receiptFile.required' => 'Please select a receipt file to upload.',
    'receiptFile.file' => 'The uploaded file must be a valid file.',
    'receiptFile.mimes' => 'Only PDF, JPG, JPEG, and PNG files are allowed.',
    'receiptFile.max' => 'The file size must not exceed 5MB.',
    'description.max' => 'The description must not exceed 500 characters.',
  ];

  public function mount()
  {
    // Ensure user is authenticated and has a customer profile
    if (!Auth::check() || !Auth::user()->customer) {
      abort(403, 'Unauthorized access.');
    }
  }

  public function updatedReceiptFile()
  {
    $this->validateOnly('receiptFile');
  }

  public function uploadReceipt()
  {
    $this->validate();

    try {
      $customer = Auth::user()->customer;

      // Store the file
      $filePath = $this->receiptFile->store('payment-receipts/' . $customer->id, 'public');

      // Create payment receipt record
      $receipt = PaymentReceipt::create([
        'customer_id' => $customer->id,
        'file_name' => basename($filePath),
        'file_path' => $filePath,
        'original_name' => $this->receiptFile->getClientOriginalName(),
        'mime_type' => $this->receiptFile->getMimeType(),
        'file_size' => $this->receiptFile->getSize(),
        'description' => $this->description,
        'status' => 'pending',
      ]);

      // Check if customer has pending delivery payments
      $pendingDelivery = \App\Models\Delivery::whereHas('event', function ($query) use ($customer) {
        $query->where('customer_id', $customer->id);
      })
        ->where('payment_status', 'pending')
        ->where('payment_method', 'offline')
        ->where('delivery_required', true)
        ->first();

      if ($pendingDelivery) {
        try {
          // Link receipt to delivery payment
          $receipt->update([
            'description' => $this->description . ' (Delivery Service Payment - ' . $pendingDelivery->deliveryService->name . ' - ₦' . number_format($pendingDelivery->cost, 2) . ')',
          ]);

          // Update delivery payment status to indicate receipt uploaded
          $pendingDelivery->update([
            'payment_status' => 'receipt_uploaded',
          ]);

          // Log the receipt upload for audit purposes
          \Log::info('Payment receipt uploaded for delivery', [
            'customer_id' => $customer->id,
            'delivery_id' => $pendingDelivery->id,
            'receipt_id' => $receipt->id,
            'amount' => $pendingDelivery->cost,
          ]);
        } catch (\Exception $relationError) {
          // Log relationship error but don't fail the upload
          \Log::warning('Failed to link receipt to delivery payment', [
            'error' => $relationError->getMessage(),
            'delivery_id' => $pendingDelivery->id ?? 'unknown',
            'receipt_id' => $receipt->id ?? 'unknown',
          ]);
        }
      }

      // Reset form
      $this->reset(['receiptFile', 'description']);

      session()->flash('message', 'Receipt uploaded successfully. It will be reviewed by our team.');
    } catch (\Exception $e) {
      // Log the actual error for debugging
      \Log::error('Payment receipt upload failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'customer_id' => Auth::user()->customer?->id ?? 'unknown',
      ]);

      session()->flash('error', 'Failed to upload receipt. Please try again.');
    }
  }

  public function confirmDelete($receiptId)
  {
    $this->confirmingDelete = true;
    $this->receiptToDelete = $receiptId;
  }

  public function deleteReceipt()
  {
    if ($this->receiptToDelete) {
      $customer = Auth::user()->customer;
      if (!$customer) {
        session()->flash('error', 'Customer profile not found.');
        return;
      }

      $receipt = PaymentReceipt::where('id', $this->receiptToDelete)
        ->where('customer_id', $customer->id)
        ->first();

      if ($receipt) {
        // Delete the file from storage
        Storage::disk('public')->delete($receipt->file_path);

        // Delete the record
        $receipt->delete();

        session()->flash('message', 'Receipt deleted successfully.');
      } else {
        session()->flash('error', 'Receipt not found or you do not have permission to delete it.');
      }
    }

    $this->confirmingDelete = false;
    $this->receiptToDelete = null;
  }

  public function cancelDelete()
  {
    $this->confirmingDelete = false;
    $this->receiptToDelete = null;
  }

  public function viewReceipt($receiptId)
  {
    $customer = Auth::user()->customer;
    if (!$customer) {
      session()->flash('error', 'Customer profile not found.');
      return;
    }

    $receipt = PaymentReceipt::where('id', $receiptId)
      ->where('customer_id', $customer->id)
      ->first();

    if ($receipt) {
      $this->viewingReceipt = $receipt;
      $this->showViewModal = true;
    } else {
      session()->flash('error', 'Receipt not found or you do not have permission to view it.');
    }
  }

  public function closeViewModal()
  {
    $this->showViewModal = false;
    $this->viewingReceipt = null;
  }

  public function render()
  {
    $customer = Auth::user()->customer;

    if (!$customer) {
      // Return empty paginated result to maintain compatibility with view
      $emptyReceipts = PaymentReceipt::whereRaw('1=0') // This will return no results
        ->orderBy('created_at', 'desc')
        ->paginate(10);

      return view('livewire.payment-receipts', [
        'receipts' => $emptyReceipts,
      ]);
    }

    $receipts = PaymentReceipt::where('customer_id', $customer->id)
      ->orderBy('created_at', 'desc')
      ->paginate(10);

    return view('livewire.payment-receipts', [
      'receipts' => $receipts,
    ]);
  }
}
