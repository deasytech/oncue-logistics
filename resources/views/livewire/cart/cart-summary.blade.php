<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Your Shopping Cart</h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">Review your customized packages before checkout</p>
        </div>

        @if (count($cartItems) === 0)
            <!-- Empty Cart State -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12 text-center">
                <div class="mb-6">
                    <svg class="w-24 h-24 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
                        </path>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Your cart is empty</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Looks like you haven't added any packages yet.</p>
                    <a href="{{ route('packages.list') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-600 to-purple-600 text-white rounded-xl font-medium hover:from-pink-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Browse Packages
                    </a>
                </div>
            </div>
        @else
            <!-- Cart Items -->
            <div class="space-y-6 mb-8">
                @foreach ($cartItems as $item)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                            <!-- Package Info Section -->
                            <div class="flex-1">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-16 h-16 bg-gradient-to-br from-pink-100 to-purple-100 dark:from-pink-900 dark:to-purple-900 rounded-xl flex items-center justify-center">
                                            <svg class="w-8 h-8 text-pink-600 dark:text-pink-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                            {{ $item->package->name }}</h2>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">
                                            {{ $item->package->description }}</p>

                                        <!-- Customization Details -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                            @if ($item->material)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-3 h-3 bg-pink-500 rounded-full"></div>
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">Material:
                                                        <span
                                                            class="font-medium text-gray-900 dark:text-white">{{ $item->material->name }}</span></span>
                                                </div>
                                            @endif
                                            @if ($item->font)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">Font: <span
                                                            class="font-medium text-gray-900 dark:text-white">{{ $item->font->name }}</span></span>
                                                </div>
                                            @endif
                                            @if ($item->color)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-3 h-3 rounded-full"
                                                        style="background-color: {{ $item->color->hex }}"></div>
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">Color: <span
                                                            class="font-medium text-gray-900 dark:text-white">{{ $item->color->name }}</span></span>
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Quantity: <span
                                                        class="font-medium text-gray-900 dark:text-white">{{ $item->quantity }}</span></span>
                                            </div>
                                        </div>

                                        <!-- Personal Message -->
                                        @if ($item->message)
                                            <div
                                                class="bg-gradient-to-r from-pink-50 to-purple-50 dark:from-pink-900/20 dark:to-purple-900/20 rounded-lg p-3 mb-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                                    Personal Message:</p>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 italic">
                                                    "{{ $item->message }}"</p>
                                            </div>
                                        @endif

                                        <!-- Delivery Note -->
                                        @if ($item->location)
                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                                    Delivery Location:</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $item->location }}</p>
                                            </div>
                                        @endif

                                        <!-- Delivery Service -->
                                        @if ($item->deliveryService)
                                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                                    Delivery Service:</p>
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $item->deliveryService->name }}
                                                        </p>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $item->deliveryService->description }}
                                                        </p>
                                                    </div>
                                                    <span
                                                        class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                                        +₦{{ number_format($item->deliveryService->cost, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Price and Actions Section -->
                            <div class="lg:w-64 flex flex-col items-start lg:items-end gap-4">
                                <div class="text-right">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Unit Price</p>
                                    <p class="text-2xl font-bold text-pink-600 dark:text-pink-400">
                                        ₦{{ number_format($item->unit_price, 2) }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total</p>
                                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                        ₦{{ number_format($item->total_price, 2) }}
                                    </p>
                                </div>

                                <button wire:click="removeFromCart('{{ $item->id }}')"
                                    wire:confirm="Are you sure you want to remove this item from your cart?"
                                    class="flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Order Summary</h3>

                <div class="space-y-4 mb-6">
                    @php
                        $subtotal = $cartItems->sum(function ($item) {
                            return $item->unit_price * $item->quantity;
                        });
                        $deliveryTotal = $cartItems->sum(function ($item) {
                            return $item->deliveryService ? $item->deliveryService->cost : 0;
                        });
                    @endphp

                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal ({{ count($cartItems) }} items)</span>
                        <span
                            class="text-lg font-semibold text-gray-900 dark:text-white">₦{{ number_format($subtotal, 2) }}</span>
                    </div>

                    @if ($deliveryTotal > 0)
                        <div
                            class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Delivery Services</span>
                            <span
                                class="text-lg font-semibold text-gray-900 dark:text-white">₦{{ number_format($deliveryTotal, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center py-3">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Total Amount</span>
                        <span
                            class="text-3xl font-bold text-purple-600 dark:text-purple-400">₦{{ number_format($totalAmount, 2) }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('packages.list') }}"
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Continue Shopping
                    </a>
                    <button wire:click="checkout" wire:loading.attr="disabled"
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white rounded-xl font-medium transition-all duration-200 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
                            </path>
                        </svg>
                        <span wire:loading.remove>Proceed to Payment</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
                <!-- Success Message -->
                @if (session()->has('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                        class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <!-- Error Message -->
                @if (session()->has('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                        class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                <script>
                    // Add smooth scrolling and enhanced interactions
                    document.addEventListener('DOMContentLoaded', function() {
                        // Smooth scroll to top when items are removed
                        Livewire.hook('message.processed', (message, component) => {
                            if (component.component.name === 'cart.cart-summary') {
                                window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });
                            }
                        });
                    });
                </script>
            </div>
        @endif
    </div>
</div>
