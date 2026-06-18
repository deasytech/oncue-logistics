    <div class="min-h-screen bg-gray-50 py-8">
        <!-- Loading Overlay - Hidden by default, only shown when initiatePayment is called -->
        <div wire:loading wire:target="initiatePayment"
            style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; display: none;"
            wire:loading.class="!flex !items-center !justify-center">
            <div
                style="background-color: white; border-radius: 0.5rem; padding: 2rem; max-width: 24rem; margin: auto; text-align: center; position: relative; z-index: 10000; transform: translate(-50%, -50%); position: absolute; top: 50%; left: 50%;">
                <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
                    <div
                        style="animation: spin 1s linear infinite; height: 3rem; width: 3rem; border-radius: 9999px; border-bottom-width: 2px; border-color: rgb(37 99 235);">
                    </div>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 600; color: rgb(17 24 39); margin-bottom: 0.5rem;">
                    Processing Payment</h3>
                <p style="color: rgb(75 85 99);">Please wait while we process your payment...</p>
                <div style="margin-top: 1rem;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <div
                            style="width: 0.5rem; height: 0.5rem; background-color: rgb(37 99 235); border-radius: 9999px; animation: bounce 1s infinite;">
                        </div>
                        <div
                            style="width: 0.5rem; height: 0.5rem; background-color: rgb(37 99 235); border-radius: 9999px; animation: bounce 1s infinite; animation-delay: 0.1s;">
                        </div>
                        <div
                            style="width: 0.5rem; height: 0.5rem; background-color: rgb(37 99 235); border-radius: 9999px; animation: bounce 1s infinite; animation-delay: 0.2s;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Checkout</h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">Review your order and complete your purchase
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button id="darkModeToggle"
                            class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 hidden dark:block" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                            </svg>
                            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 block dark:hidden" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                    1</div>
                                <span class="ml-2 text-sm font-medium text-pink-600 dark:text-pink-400">Cart</span>
                            </div>
                            <div class="w-12 h-0.5 bg-pink-600"></div>
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                    2</div>
                                <span class="ml-2 text-sm font-medium text-pink-600 dark:text-pink-400">Checkout</span>
                            </div>
                            <div class="w-12 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-400 text-sm font-medium">
                                    3</div>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Payment</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Order Summary -->
                    <div class="lg:col-span-2">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Order Summary</h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $items->count() }} item(s)
                                    in
                                    your
                                    cart</p>
                            </div>

                            @if (session()->has('error'))
                                <div
                                    class="mx-6 mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-400 dark:text-red-500 mr-3" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <h3 class="text-sm font-medium text-red-800 dark:text-red-400">Error</h3>
                                            <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                                                {{ session('error') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="p-6">
                                @if ($items->isEmpty())
                                    <div class="text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Your cart is
                                            empty
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start shopping to add
                                            items
                                            to
                                            your cart.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('packages.list') }}"
                                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                                                Browse Packages
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-4">
                                        @foreach ($items as $item)
                                            <div
                                                class="flex items-center space-x-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3
                                                        class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                        {{ $item->package->name ?? 'Custom Package' }}
                                                    </h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                        @if ($item->material)
                                                            Material: {{ $item->material->name }} •
                                                        @endif
                                                        @if ($item->color)
                                                            Color: {{ $item->color->name }} •
                                                        @endif
                                                        @if ($item->font)
                                                            Font: {{ $item->font->name }}
                                                        @endif
                                                    </p>
                                                    <div class="flex items-center mt-2">
                                                        <span
                                                            class="text-sm text-gray-500 dark:text-gray-400">Quantity:</span>
                                                        <span
                                                            class="ml-1 text-sm font-medium text-gray-900 dark:text-white">{{ $item->quantity }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        ₦{{ number_format($item->total_price, 2) }}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        ₦{{ number_format($item->unit_price, 2) }}
                                                        each</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div
                            class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Customer Information
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ Auth::user()->email }}</p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ Auth::user()->customer->phone ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ Auth::user()->customer->location ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="lg:col-span-1">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 sticky top-8">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Payment Summary</h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Subtotal ({{ $items->count() }}
                                            items)</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">₦{{ number_format($total, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                        <span class="font-medium text-green-600">Free</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                        <span class="font-medium text-gray-900 dark:text-white">₦0.00</span>
                                    </div>
                                    <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                                        <div class="flex justify-between">
                                            <span
                                                class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                                            <span
                                                class="text-2xl font-bold text-gray-900 dark:text-white">₦{{ number_format($total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Method Selection -->
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Method
                                    </h3>
                                    <div class="space-y-3">
                                        <label
                                            class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <input type="radio" wire:model="paymentMethod" value="online"
                                                class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-900 dark:text-white">Online
                                                        Payment</span>
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pay securely
                                                    with
                                                    Paystack (Cards, Bank Transfer, USSD)</p>
                                            </div>
                                        </label>

                                    </div>
                                </div>


                                <div class="mt-6">
                                    <button wire:click="initiatePayment" wire:loading.attr="disabled"
                                        @if ($items->isEmpty()) disabled @endif
                                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-300 dark:disabled:bg-gray-600 disabled:cursor-not-allowed transition-colors duration-200 flex items-center justify-center space-x-2 cursor-pointer">
                                        <span wire:loading wire:target="initiatePayment" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Processing...
                                        </span>
                                        <span wire:loading.remove wire:target="initiatePayment"
                                            class="flex items-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            <span>Proceed to Payment</span>
                                        </span>
                                    </button>
                                </div>

                                <div class="mt-4 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Secure payment powered by Paystack
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div
                            class="mt-6 bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-pink-400 dark:text-pink-500 mt-0.5 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-pink-800 dark:text-pink-400">Secure Checkout
                                    </h3>
                                    <p class="mt-1 text-sm text-pink-700 dark:text-pink-300">Your payment information
                                        is
                                        encrypted and secure. We
                                        never store your payment details.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                .sticky {
                    position: sticky;
                    top: 2rem;
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                // Dark mode toggle functionality
                const darkModeToggle = document.getElementById('darkModeToggle');
                const html = document.documentElement;

                // Check for saved dark mode preference or default to light mode
                const currentTheme = localStorage.getItem('theme') || 'light';
                if (currentTheme === 'dark') {
                    html.classList.add('dark');
                }

                // Toggle dark mode
                darkModeToggle.addEventListener('click', () => {
                    if (html.classList.contains('dark')) {
                        html.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        html.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                });
            </script>
        @endpush
