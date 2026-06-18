<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <!-- Payment Summary Cards - Vertical Column Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700 w-full">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                ₦{{ number_format($paymentStats['total_revenue'] ?? 0, 2) }}
                            </p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Customer Payments</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                ₦{{ number_format($paymentStats['total_customer_payments'] ?? 0, 2) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $paymentStats['customer_payments_count'] ?? 0 }} transactions
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Guest Fabric Sales</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                ₦{{ number_format($paymentStats['total_guest_fabric_payments'] ?? 0, 2) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $paymentStats['guest_fabric_payments_count'] ?? 0 }} sales
                            </p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Payment Success Rate</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $paymentStats['total_revenue'] > 0 ? number_format((($paymentStats['customer_payments_count'] + $paymentStats['guest_fabric_payments_count']) / max(1, $paymentStats['customer_payments_count'] + $paymentStats['guest_fabric_payments_count'])) * 100, 1) : 0 }}%
                            </p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Order Value</p>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                ₦{{ $paymentStats['customer_payments_count'] + $paymentStats['guest_fabric_payments_count'] > 0 ? number_format($paymentStats['total_revenue'] / ($paymentStats['customer_payments_count'] + $paymentStats['guest_fabric_payments_count']), 2) : 0 }}
                            </p>
                        </div>
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Filter and Payments by Event -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700 w-full">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 md:mb-0">Payments by Event</h3>
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Event:</label>
                        <select id="eventFilter"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">All Events</option>
                            @foreach ($eventsForFilter as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Event Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Customer Payments</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Guest Fabric Sales</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="paymentsTableBody"
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Payments</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Customer/Guest</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Event</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Amount</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($recentPayments as $payment)
                                <tr>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        @if ($payment->payment_type === 'customer')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                Customer Payment
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                                Guest Fabric
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if ($payment->payment_type === 'customer')
                                            @if ($payment->customer)
                                                {{ $payment->customer->title }} {{ $payment->customer->first_name }}
                                                {{ $payment->customer->last_name }}
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">Customer not found</span>
                                            @endif
                                        @else
                                            @if ($payment->guest)
                                                {{ $payment->guest->first_name }} {{ $payment->guest->last_name }}
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">Guest not found</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if ($payment->payment_type === 'customer')
                                            @if ($payment->customer && $payment->customer->events->first())
                                                {{ $payment->customer->events->first()->name }}
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                            @endif
                                        @else
                                            @if ($payment->event)
                                                {{ $payment->event->name }}
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        ₦{{ number_format($payment->amount ?? $payment->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $payment->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eventFilter = document.getElementById('eventFilter');
        const paymentsTableBody = document.getElementById('paymentsTableBody');

        // Load initial data
        loadPaymentsByEvent();

        // Event filter change handler
        eventFilter.addEventListener('change', function() {
            loadPaymentsByEvent(this.value);
        });

        async function loadPaymentsByEvent(eventId = null) {
            try {
                // This would typically make an AJAX request to a Livewire component
                // For now, we'll use the initial data passed to the view
                const response = await fetch(`/api/payments-by-event?event_id=${eventId || ''}`);
                const data = await response.json();
                renderPaymentsTable(data);
            } catch (error) {
                console.error('Error loading payments:', error);
                // Fallback to initial data
                renderPaymentsTable(@json($paymentsByEvent ?? []));
            }
        }

        function renderPaymentsTable(data) {
            paymentsTableBody.innerHTML = '';

            if (data.length === 0) {
                paymentsTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        No payment data available
                    </td>
                </tr>
            `;
                return;
            }

            data.forEach(event => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                    ${event.name}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    ₦${(event.customer_payments || 0).toLocaleString()}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    ₦${(event.guest_fabric_payments || 0).toLocaleString()}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                    ₦${((event.customer_payments || 0) + (event.guest_fabric_payments || 0)).toLocaleString()}
                </td>
            `;
                paymentsTableBody.appendChild(row);
            });
        }
    });
</script>
