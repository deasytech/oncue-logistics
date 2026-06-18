<?php

namespace App\Livewire\Cart;

use App\Models\PackageCustomization;
use App\Models\PackagePayment;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Str;

class Checkout extends Component
{
    public $total;
    public $items;
    public $paymentMethod = 'online'; // Only online payment available
    public $customerName;
    public $customerPhone;
    public $customerEmail;
    public $customerAddress;
    public $isProcessing = false;

    public function mount()
    {
        $customerId = Auth::user()->customer->id ?? null;

        $this->items = PackageCustomization::where('customer_id', $customerId)
            ->where('status', 'in_cart')
            ->with(['package'])
            ->get();

        $this->total = $this->items->sum('total_price');

        // Pre-fill customer information
        $this->customerName = Auth::user()->name;
        $this->customerEmail = Auth::user()->email;
        $this->customerPhone = Auth::user()->customer->phone ?? '';
        $this->customerAddress = Auth::user()->customer->location ?? '';
    }

    public function initiatePayment()
    {
        if ($this->items->isEmpty()) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        // Set processing state before any operations
        $this->isProcessing = true;
        $this->dispatch('payment-processing-started');

        return $this->processOnlinePayment();
    }

    public function processOnlinePayment()
    {
        $reference = Str::uuid()->toString();

        // Create payment record with customer info for online payment
        $payment = PackagePayment::create([
            'customer_id' => Auth::user()->customer->id,
            'reference' => $reference,
            'amount' => $this->total,
            'items' => $this->items->toArray(),
            'payment_method' => 'online',
            'status' => 'pending',
            'customer_info' => [
                'name' => $this->customerName,
                'email' => $this->customerEmail,
                'phone' => $this->customerPhone,
                'address' => $this->customerAddress,
            ],
        ]);

        $email = Auth::user()->email;
        $callback = route('payment.callback');

        $response = Http::withToken(config('services.paystack.secret'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $email,
                'amount' => $this->total * 100,
                'reference' => $reference,
                'callback_url' => $callback,
            ]);

        $data = $response->json();

        if ($data['status'] === true) {
            // Send initial order confirmation email for online payments
            try {
                Mail::to($this->customerEmail)->send(new OrderConfirmationMail($payment, $this->items));
                Log::info('Order confirmation email sent for online payment', [
                    'payment_reference' => $reference,
                    'customer_email' => $this->customerEmail,
                    'payment_method' => 'online'
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email for online payment: ' . $e->getMessage());
            }

            return redirect()->away($data['data']['authorization_url']);
        } else {
            session()->flash('error', 'Payment initialization failed.');
        }
    }


    public function render()
    {
        return view('livewire.cart.checkout');
    }
}
