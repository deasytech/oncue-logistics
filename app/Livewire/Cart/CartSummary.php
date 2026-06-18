<?php

namespace App\Livewire\Cart;

use App\Models\PackageCustomization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartSummary extends Component
{
    /** @var \Illuminate\Support\Collection */
    public $items;
    public $total = 0;
    public $showSuccessMessage = false;

    public function mount()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        $customerId = Auth::user()->customer->id ?? null;

        if (!$customerId) {
            $this->items = collect();
            $this->total = 0;
            return;
        }

        $this->items = PackageCustomization::where('customer_id', $customerId)
            ->where('status', 'in_cart')
            ->with(['package', 'material', 'font', 'color', 'deliveryService'])
            ->orderBy('created_at', 'desc')
            ->get();

        $this->total = $this->items->sum('total_price');
    }

    public function removeFromCart($id)
    {
        $item = PackageCustomization::find($id);

        if (!$item) {
            session()->flash('error', 'Item not found.');
            return;
        }

        if ($item->customer_id !== Auth::user()->customer->id) {
            abort(403);
        }

        $item->delete();

        session()->flash('success', 'Item removed from cart successfully.');
        $this->showSuccessMessage = true;
        $this->loadItems();

        // Hide success message after 3 seconds
        $this->dispatch('hide-success-message');
    }

    public function updateQuantity($id, $quantity)
    {
        $item = PackageCustomization::find($id);

        if (!$item || $item->customer_id !== Auth::user()->customer->id) {
            session()->flash('error', 'Item not found or unauthorized.');
            return;
        }

        if ($quantity < 1) {
            session()->flash('error', 'Quantity must be at least 1.');
            return;
        }

        $item->update([
            'quantity' => $quantity,
            'total_price' => $item->unit_price * $quantity
        ]);

        session()->flash('success', 'Quantity updated successfully.');
        $this->loadItems();
    }

    public function checkout()
    {
        if ($this->items->isEmpty()) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        // Store cart data in session for checkout
        session(['checkout_items' => $this->items, 'checkout_total' => $this->total]);

        // Redirect to checkout page (you'll need to create this route)
        return redirect()->route('cart.checkout');
    }

    public function render()
    {
        return view('livewire.cart.cart-summary', [
            'cartItems' => $this->items,
            'totalAmount' => $this->total,
        ]);
    }
}
