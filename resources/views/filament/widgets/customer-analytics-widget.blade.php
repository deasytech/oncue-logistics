<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-user-group" icon-color="info" collapsible>
        <x-slot name="heading">Customer Analytics</x-slot>
        <x-slot name="description">Overview of customer growth and engagement</x-slot>
        <x-slot name="headerEnd">
            <x-filament::badge color="gray" size="sm">
                {{ number_format($customerStats['total']) }} total
            </x-filament::badge>
        </x-slot>

        {{-- 4-column main content grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Col 1: Growth chart --}}
            <div class="sm:col-span-1">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-1.5">
                    <x-heroicon-o-chart-bar class="w-4 h-4 text-gray-400" />
                    Growth (6 Months)
                </h3>
                <div class="space-y-2">
                    @php $maxCount = collect($growthData)->max('count') ?: 1; @endphp
                    @foreach ($growthData as $data)
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400 w-14 shrink-0">
                                {{ $data['month'] }}
                            </span>
                            <div class="flex-1 bg-gray-100 dark:bg-white/10 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-500 dark:bg-blue-400 h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $maxCount > 0 ? ($data['count'] / $maxCount) * 100 : 0 }}%">
                                </div>
                            </div>
                            <span
                                class="text-xs font-semibold text-gray-700 dark:text-gray-300 w-5 text-right shrink-0">
                                {{ $data['count'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Col 2: Top customers by events --}}
            <div class="sm:col-span-1">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-1.5">
                    <x-heroicon-o-star class="w-4 h-4 text-gray-400" />
                    Top by Events
                </h3>
                <div class="space-y-1.5">
                    @forelse ($topCustomers as $customer)
                        <a href="{{ url('/admin/customers/' . $customer->id) }}"
                            class="flex items-center justify-between px-2.5 py-2 rounded-lg bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors group">
                            <p
                                class="text-xs font-medium text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $customer->first_name }} {{ $customer->last_name }}
                            </p>
                            <x-filament::badge color="primary" size="sm" class="ml-2 shrink-0">
                                {{ $customer->events_count }}
                            </x-filament::badge>
                        </a>
                    @empty
                        <p class="text-xs text-gray-400 dark:text-gray-500 py-4 text-center">No data yet</p>
                    @endforelse
                </div>
            </div>

            {{-- Col 3 & 4: Recently active customers (spans 2 cols) --}}
            <div class="sm:col-span-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-1.5">
                    <x-heroicon-o-bolt class="w-4 h-4 text-gray-400" />
                    Recently Active
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @forelse ($recentActiveCustomers as $customer)
                        <a href="{{ url('/admin/customers/' . $customer->id) }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors group">
                            <div class="min-w-0">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $customer->title }} {{ $customer->first_name }} {{ $customer->last_name }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $customer->email }}</p>
                            </div>
                            <span
                                class="ml-2 shrink-0 inline-flex h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-emerald-500/20"></span>
                        </a>
                    @empty
                        <div class="col-span-full py-4 text-center text-sm text-gray-400 dark:text-gray-500">
                            No active customers found
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <x-slot name="footer">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $customerStats['active'] }} active of {{ $customerStats['total'] }} total customers
                </p>
                <a href="{{ url('/admin/customers') }}"
                    class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    View all customers
                    <x-heroicon-m-arrow-right class="h-3.5 w-3.5" />
                </a>
            </div>
        </x-slot>
    </x-filament::section>
</x-filament-widgets::widget>
