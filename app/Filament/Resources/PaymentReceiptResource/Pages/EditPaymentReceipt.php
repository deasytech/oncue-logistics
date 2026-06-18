<?php

namespace App\Filament\Resources\PaymentReceiptResource\Pages;

use App\Filament\Resources\PaymentReceiptResource;
use App\Mail\PaymentReceiptApprovedMail;
use App\Models\Delivery;
use App\Models\PaymentReceipt;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EditPaymentReceipt extends EditRecord
{
  protected static string $resource = PaymentReceiptResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

  protected function handleRecordUpdate(Model $record, array $data): Model
  {
    // Update the receipt status
    $record->update([
      'status' => $data['status'],
      'admin_notes' => $data['admin_notes'],
    ]);

    // If the receipt is approved, update the associated delivery payment status
    if ($data['status'] === 'approved') {
      $this->approveAssociatedDelivery($record);
    } elseif ($data['status'] === 'rejected') {
      // If rejected, reset delivery payment status back to pending
      $this->rejectAssociatedDelivery($record);
    }

    return $record;
  }

  private function approveAssociatedDelivery(Model $receipt): void
  {
    try {
      // Find the delivery associated with this receipt
      $delivery = Delivery::whereHas('event', function ($query) use ($receipt) {
        $query->where('customer_id', $receipt->customer_id);
      })
        ->where('payment_status', 'receipt_uploaded')
        ->where('payment_method', 'offline')
        ->where('delivery_required', true)
        ->first();

      if ($delivery) {
        // Update delivery payment status to paid
        $delivery->update([
          'payment_status' => 'paid',
          'paid_at' => now(),
        ]);

        // Send approval email to customer
        $this->sendApprovalEmail($receipt);

        // Log the approval for audit purposes
        \Log::info('Payment receipt approved and delivery payment updated', [
          'receipt_id' => $receipt->id,
          'customer_id' => $receipt->customer_id,
          'delivery_id' => $delivery->id,
          'amount' => $delivery->cost,
        ]);
      }
    } catch (\Exception $e) {
      \Log::error('Failed to approve associated delivery for receipt', [
        'receipt_id' => $receipt->id,
        'error' => $e->getMessage(),
      ]);
    }
  }

  private function sendApprovalEmail(Model $receipt): void
  {
    try {
      $customer = $receipt->customer;

      if ($customer && $customer->email) {
        Mail::to($customer->email)->send(new PaymentReceiptApprovedMail($receipt));

        // Log email sending
        \Log::info('Payment approval email sent to customer', [
          'customer_id' => $customer->id,
          'email' => $customer->email,
          'receipt_id' => $receipt->id,
        ]);

        // Show success notification to admin
        Notification::make()
          ->title('Approval Email Sent')
          ->body('Customer has been notified about the payment approval via email.')
          ->success()
          ->send();
      }
    } catch (\Exception $e) {
      \Log::error('Failed to send approval email to customer', [
        'customer_id' => $receipt->customer_id,
        'error' => $e->getMessage(),
      ]);

      // Show warning notification to admin about email failure
      Notification::make()
        ->title('Email Notification Failed')
        ->body('Payment was approved but email notification could not be sent. Customer may not be aware of the approval.')
        ->warning()
        ->send();
    }
  }

  private function rejectAssociatedDelivery(Model $receipt): void
  {
    try {
      // Find the delivery associated with this receipt and reset status
      $delivery = Delivery::whereHas('event', function ($query) use ($receipt) {
        $query->where('customer_id', $receipt->customer_id);
      })
        ->where('payment_status', 'receipt_uploaded')
        ->where('payment_method', 'offline')
        ->where('delivery_required', true)
        ->first();

      if ($delivery) {
        // Reset delivery payment status back to pending
        $delivery->update([
          'payment_status' => 'pending',
        ]);

        // Log the rejection for audit purposes
        \Log::info('Payment receipt rejected and delivery payment reset', [
          'receipt_id' => $receipt->id,
          'customer_id' => $receipt->customer_id,
          'delivery_id' => $delivery->id,
        ]);
      }
    } catch (\Exception $e) {
      \Log::error('Failed to reject associated delivery for receipt', [
        'receipt_id' => $receipt->id,
        'error' => $e->getMessage(),
      ]);
    }
  }
}
