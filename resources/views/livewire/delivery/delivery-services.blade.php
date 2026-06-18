<div>
    <div class="max-w-5xl mx-auto py-8 sm:px-6 lg:px-8" x-data="{ paymentSubmitting: false }">
        <!-- Listen for Paystack redirect event -->
        <div
            x-on:redirectToPaystack.window="(event) => {
            console.log('Redirecting to Paystack for delivery:', event.detail.deliveryId);
            window.location.href = '{{ route('delivery.paystack.redirect') }}?delivery_id=' + event.detail.deliveryId;
        }">
            <div
                class="bg-white dark:bg-zinc-800 overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-200 dark:border-zinc-700">
                <div class="px-6 py-8 sm:p-10">
                    <!-- Header Section -->
                    <div class="text-center mb-10">
                        <div
                            class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-pink-100 to-pink-200 dark:from-pink-900/30 dark:to-pink-800/30 flex items-center justify-center">
                            <svg class="w-10 h-10 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Delivery Services</h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                            Choose the perfect delivery service for your event. All services include professional
                            handling
                            and timely delivery.
                        </p>
                    </div>

                    <!-- Alert Messages -->
                    @if (session()->has('message'))
                        <div
                            class="mb-8 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span
                                    class="text-emerald-700 dark:text-emerald-300 font-medium">{{ session('message') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div
                            class="mb-8 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Service Selection Form -->
                    <form wire:submit.prevent="selectService">
                        <div class="mb-10">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Available
                                Services</h3>
                            <div class="grid gap-6">
                                @foreach ($deliveryServices as $service)
                                    <div class="relative border-2 rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:shadow-lg hover:scale-[1.02] group
                                    {{ $selectedServiceId == $service->id ? 'border-pink-500 bg-pink-50 dark:bg-pink-900/20 shadow-lg' : 'border-gray-200 dark:border-zinc-600 hover:border-pink-300 dark:hover:border-pink-600' }}"
                                        wire:click="$set('selectedServiceId', {{ $service->id }})">

                                        <!-- Selection Indicator -->
                                        <div class="absolute top-4 right-4">
                                            <div
                                                class="w-6 h-6 rounded-full border-2 {{ $selectedServiceId == $service->id ? 'border-pink-500 bg-pink-500' : 'border-gray-300 dark:border-zinc-500' }} flex items-center justify-center transition-all duration-200">
                                                @if ($selectedServiceId == $service->id)
                                                    <svg class="w-3 h-3 text-white" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-4">
                                                <div class="flex-shrink-0">
                                                    <input type="radio" name="delivery_service"
                                                        value="{{ $service->id }}" wire:model="selectedServiceId"
                                                        class="sr-only">
                                                    <div
                                                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-100 to-pink-200 dark:from-pink-900/30 dark:to-pink-800/30 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-pink-600 dark:text-pink-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                                        {{ $service->name }}</h4>
                                                    <p class="text-gray-600 dark:text-gray-400 mb-3 leading-relaxed">
                                                        {{ $service->description }}</p>
                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ ucfirst($service->applicable_to) }} Events
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($service->cost != 0)
                                                <div class="text-right ml-4">
                                                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                                                        ₦{{ number_format($service->cost, 2) }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                        Fixed Price
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('selectedServiceId')
                                <p class="mt-3 text-sm text-red-600 dark:text-red-400 font-medium text-center">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-center space-x-4">
                            <a href="{{ route('events.list') }}"
                                class="inline-flex items-center px-8 py-3 border-2 border-gray-300 dark:border-zinc-600 rounded-xl text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:border-gray-400 dark:hover:border-zinc-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 dark:focus:ring-offset-zinc-800 transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 dark:focus:ring-offset-zinc-800 transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                Select Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Enhanced Payment Method Modal -->
        @if ($showPaymentModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 transition-opacity backdrop-blur-sm"
                        wire:click="cancelPayment" aria-hidden="true"></div>

                    <!-- Modal panel -->
                    <div
                        class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full mx-4 border border-gray-200 dark:border-zinc-700">

                        <!-- Modal Header -->
                        <div
                            class="bg-gradient-to-r from-pink-600 to-pink-700 dark:from-pink-700 dark:to-pink-800 px-6 py-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-2xl font-bold text-white" id="modal-title">
                                    Delivery Payment Details
                                </h3>
                                <button wire:click="cancelPayment"
                                    class="text-white hover:text-pink-100 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-8">
                            <div class="space-y-6">
                                <!-- Event Selection -->
                                <div class="space-y-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Select Event
                                    </label>
                                    <select wire:model.live="selectedEventId"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 dark:bg-zinc-700 dark:text-white">
                                        <option value="">-- Select an event --</option>
                                        @foreach (Auth::user()->customer->events as $event)
                                            <option value="{{ $event->id }}">
                                                {{ $event->name }} ({{ $event->guests->count() }} guests)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedEventId')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Online Payment Option -->
                                <div class="relative border-2 rounded-xl p-6 cursor-pointer transition-all duration-300 hover:shadow-lg
                                {{ $paymentMethod == 'online' ? 'border-pink-500 bg-pink-50 dark:bg-pink-900/20 shadow-lg' : 'border-gray-200 dark:border-zinc-600 hover:border-pink-300 dark:hover:border-pink-600' }}"
                                    wire:click="$set('paymentMethod', 'online')">

                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                                        Online
                                                        Payment</h4>
                                                    <p class="text-gray-600 dark:text-gray-400">Pay securely with your
                                                        card
                                                        using Paystack</p>
                                                </div>
                                                <div
                                                    class="w-6 h-6 rounded-full border-2 {{ $paymentMethod == 'online' ? 'border-pink-500 bg-pink-500' : 'border-gray-300 dark:border-zinc-500' }} flex items-center justify-center transition-all duration-200">
                                                    @if ($paymentMethod == 'online')
                                                        <svg class="w-3 h-3 text-white" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            @error('paymentMethod')
                                <p class="mt-4 text-sm text-red-600 dark:text-red-400 font-medium text-center">
                                    {{ $message }}</p>
                            @enderror


                            <!-- Cost Breakdown -->
                            @if ($selectedEventId && $selectedServiceId)
                                <div wire:key="cost-{{ $selectedEventId }}-{{ $selectedServiceId }}"
                                    class="mt-6 p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-zinc-700 dark:to-zinc-600 rounded-xl border border-gray-200 dark:border-zinc-500">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cost Breakdown
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Service Cost (per
                                                delivery)</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                ₦{{ number_format($deliveryCost, 2) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Number of
                                                Guests</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $guestCount }}
                                            </span>
                                        </div>
                                        <div class="border-t border-gray-300 dark:border-zinc-600 pt-3">
                                            <div class="flex justify-between">
                                                <span class="text-lg font-semibold text-gray-900 dark:text-white">Total
                                                    Amount</span>
                                                <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                                    ₦{{ number_format($totalCost, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Total Amount Display -->
                            <div
                                class="mt-6 p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-zinc-700 dark:to-zinc-600 rounded-xl border border-gray-200 dark:border-zinc-500">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Amount to Pay
                                        </p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                            ₦{{ number_format($totalCost, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Selected Service</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $deliveryServices->where('id', $selectedServiceId)->first()->name ?? 'No service selected' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div
                            class="bg-gray-50 dark:bg-zinc-700/50 px-6 py-6 border-t border-gray-200 dark:border-zinc-600">
                            <div class="flex justify-center space-x-4">
                                <button type="button" wire:click="cancelPayment" wire:loading.attr="disabled"
                                    wire:target="cancelPayment,processPayment"
                                    class="inline-flex items-center px-6 py-3 border-2 border-gray-300 dark:border-zinc-600 rounded-xl text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-600 hover:border-gray-400 dark:hover:border-zinc-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 dark:focus:ring-offset-zinc-800 transition-all duration-200 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="cancelPayment,processPayment">Cancel</span>
                                    <span wire:loading wire:target="cancelPayment">Closing...</span>
                                    <span wire:loading wire:target="processPayment">Please wait...</span>
                                </button>
                                <button type="button" wire:click="processPayment"
                                    x-on:click="paymentSubmitting = true" wire:loading.attr="disabled"
                                    wire:target="processPayment"
                                    class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-zinc-800 transition-all duration-200 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg wire:loading.remove wire:target="processPayment" x-show="!paymentSubmitting"
                                        class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    <svg wire:loading wire:target="processPayment"
                                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span wire:loading.remove wire:target="processPayment"
                                        x-show="!paymentSubmitting">
                                        Proceed with Payment
                                    </span>
                                    <span wire:loading wire:target="processPayment">
                                        Redirecting to online payment...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Post-Payment Modal -->
        @if ($showPostPaymentModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="post-payment-modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 transition-opacity backdrop-blur-sm"
                        wire:click="$set('showPostPaymentModal', false)" aria-hidden="true"></div>

                    <!-- Modal panel -->
                    <div
                        class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full mx-4 border border-gray-200 dark:border-zinc-700">

                        <!-- Modal Header -->
                        <div
                            class="bg-gradient-to-r from-green-600 to-green-700 dark:from-green-700 dark:to-green-800 px-6 py-6">
                            <div class="flex items-center justify-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 text-center">
                                    <h3 class="text-2xl font-bold text-white" id="post-payment-modal-title">
                                        Payment Successful!
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-8">
                            <div class="text-center space-y-6">
                                <p class="text-lg text-gray-700 dark:text-gray-300">
                                    Thank you for your payment. You may proceed to select a packaging option for your
                                    event (optional), or end your session and return later.
                                </p>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div
                            class="bg-gray-50 dark:bg-zinc-700/50 px-6 py-6 border-t border-gray-200 dark:border-zinc-600">
                            <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
                                <button type="button" wire:click="selectPackaging"
                                    class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 dark:focus:ring-offset-zinc-800 transition-all duration-200 transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Select Packaging
                                </button>
                                <button type="button" wire:click="endSession"
                                    class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-zinc-800 transition-all duration-200 transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    End Session
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- End Session Modal -->
        @if ($showEndSessionModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="end-session-modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 transition-opacity backdrop-blur-sm"
                        wire:click="$set('showEndSessionModal', false)" aria-hidden="true"></div>

                    <!-- Modal panel -->
                    <div
                        class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full mx-4 border border-gray-200 dark:border-zinc-700">

                        <!-- Modal Header -->
                        <div
                            class="bg-gradient-to-r from-pink-600 to-pink-700 dark:from-pink-700 dark:to-pink-800 px-6 py-6">
                            <div class="flex items-center justify-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4 text-center">
                                    <h3 class="text-2xl font-bold text-white" id="end-session-modal-title">
                                        Session Ended
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-8">
                            <div class="text-center space-y-6">
                                <p class="text-lg text-gray-700 dark:text-gray-300">
                                    Thank you for choosing On Cue Logistics as your trusted event logistics partner.
                                </p>

                                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-6">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4 text-left">
                                        You can log in at any time to:
                                    </h4>
                                    <ul class="text-left space-y-2 text-gray-600 dark:text-gray-400">
                                        <li class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                            Track event updates
                                        </li>
                                        <li class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                                </path>
                                            </svg>
                                            Monitor RSVP responses
                                        </li>
                                        <li class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                </path>
                                            </svg>
                                            Track and reconcile payments
                                        </li>
                                        <li class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            Follow delivery progress
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div
                            class="bg-gray-50 dark:bg-zinc-700/50 px-6 py-6 border-t border-gray-200 dark:border-zinc-600">
                            <div class="flex justify-center">
                                <button type="button" wire:click="closeEndSessionModal"
                                    class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 dark:focus:ring-offset-zinc-800 transition-all duration-200 transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                    Go to Dashboard
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
