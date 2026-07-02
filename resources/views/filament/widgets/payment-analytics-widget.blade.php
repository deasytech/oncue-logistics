<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-clock" icon-color="info" collapsible>
        <x-slot name="heading">Recent Payments</x-slot>
        <x-slot name="description">Latest package and fabric transactions</x-slot>
        <x-slot name="headerEnd">
            <x-filament::badge color="gray" size="sm">
                {{ $paymentStats['total_transactions'] ?? 0 }} total
            </x-filament::badge>
        </x-slot>

        <div class="overflow-x-auto rounded-xl ring-1 ring-inset ring-gray-950/5 dark:ring-white/10">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-white/10">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Name</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Event</th>
                        <th
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Amount</th>
                        <th
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse ($recentPayments as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($payment->payment_type === 'customer')
                                    <x-filament::badge color="info" size="sm">Package</x-filament::badge>
                                @else
                                    <x-filament::badge color="warning" size="sm">Fabric</x-filament::badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $payment->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $payment->event }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                                    ₦{{ number_format($payment->amount ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500 text-right whitespace-nowrap">
                                {{ $payment->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center">
                                <x-heroicon-o-banknotes class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600 mb-2" />
                                <p class="text-sm text-gray-400 dark:text-gray-500">No recent payments</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footer">
            <a href="{{ url('/admin/package-payments') }}"
                class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                View all payments
                <x-heroicon-m-arrow-right class="h-3.5 w-3.5" />
            </a>
        </x-slot>
    </x-filament::section>
</x-filament-widgets::widget>
