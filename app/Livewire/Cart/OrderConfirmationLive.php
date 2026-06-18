<?php

namespace App\Livewire\Cart;

use App\Models\PackagePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class OrderConfirmationLive extends Component
{
    public $payment;
    public $reference;

    public function mount($reference)
    {
        $this->reference = $reference;

        // Get payment record with customer check - accept both online and offline payments
        $this->payment = PackagePayment::where('reference', $reference)
            ->where('customer_id', Auth::user()->customer->id)
            ->first();

        if (!$this->payment) {
            session()->flash('error', 'Order not found or you do not have permission to view this order.');
            return redirect()->route('cart.checkout');
        }

        // Debug: Log payment details to help troubleshoot
        Log::info('Order Confirmation Debug', [
            'reference' => $reference,
            'payment_method' => $this->payment->payment_method,
            'payment_id' => $this->payment->id,
            'customer_id' => $this->payment->customer_id,
            'auth_customer_id' => Auth::user()->customer->id
        ]);
    }

    public function render()
    {
        return view('livewire.cart.order-confirmation-live', [
            'payment' => $this->payment
        ]);
    }
}
