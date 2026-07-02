<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-swatch" icon-color="warning" collapsible>
        <x-slot name="heading">Fabric Revenue by Event</x-slot>
        <x-slot name="description">Guest fabric sales grouped by event</x-slot>

        <div class="overflow-x-auto rounded-xl ring-1 ring-inset ring-gray-950/5 dark:ring-white/10">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-white/10">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Event</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Customer</th>
                        <th
                            class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Orders</th>
                        <th
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse ($paymentsByEvent as $event)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $event->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $event->customer?->full_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-filament::badge color="gray" size="sm">
                                    {{ $event->paid_fabric_count ?? 0 }}
                                </x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                                    ₦{{ number_format($event->guest_fabric_selections_sum_total_amount ?? 0, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center">
                                <x-heroicon-o-swatch class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600 mb-2" />
                                <p class="text-sm text-gray-400 dark:text-gray-500">No fabric payments recorded yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footer">
            <a href="{{ url('/admin/guest-orders') }}"
                class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                View all fabric orders
                <x-heroicon-m-arrow-right class="h-3.5 w-3.5" />
            </a>
        </x-slot>
    </x-filament::section>
</x-filament-widgets::widget>
