<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Analytics</h2>

            {{-- Customer Statistics --}}
            <div class="flex flex-col md:flex-row gap-4">
                <div
                    class="w-full p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-700 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-1">Total Customers</p>
                            <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">
                                {{ number_format($customerStats['total']) }}
                            </p>
                            <div class="mt-2 text-xs text-blue-600 dark:text-blue-400">
                                Active customers
                            </div>
                        </div>
                        <div class="p-3 bg-blue-200 dark:bg-blue-800 rounded-full">
                            <x-heroicon-o-user-group class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>

                <div
                    class="w-full p-6 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-700 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-green-600 dark:text-green-400 mb-1">Active Customers</p>
                            <p class="text-3xl font-bold text-green-900 dark:text-green-100">
                                {{ number_format($customerStats['active']) }}
                            </p>
                            <div class="mt-2 text-xs text-green-600 dark:text-green-400">
                                {{ $customerStats['active_percentage'] }}% of total
                            </div>
                        </div>
                        <div class="p-3 bg-green-200 dark:bg-green-800 rounded-full">
                            <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                </div>

                <div
                    class="w-full p-6 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/20 rounded-xl border border-purple-200 dark:border-purple-700 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-purple-600 dark:text-purple-400 mb-1">New This Month</p>
                            <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">
                                {{ number_format($customerStats['new_this_month']) }}
                            </p>
                            <div class="mt-2 text-xs">
                                @if ($customerStats['growth_rate'] > 0)
                                    <span class="text-green-600 dark:text-green-400">↑
                                        +{{ $customerStats['growth_rate'] }}%</span>
                                @elseif($customerStats['growth_rate'] < 0)
                                    <span class="text-red-600 dark:text-red-400">↓
                                        {{ $customerStats['growth_rate'] }}%</span>
                                @else
                                    <span class="text-gray-600 dark:text-gray-400">→ 0%</span>
                                @endif
                                <span class="text-purple-600 dark:text-purple-400"> vs last month</span>
                            </div>
                        </div>
                        <div class="p-3 bg-purple-200 dark:bg-purple-800 rounded-full">
                            <x-heroicon-o-plus-circle class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </div>

                <div
                    class="w-full p-6 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/20 rounded-xl border border-orange-200 dark:border-orange-700 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-orange-600 dark:text-orange-400 mb-1">Avg Events/Customer
                            </p>
                            <p class="text-3xl font-bold text-orange-900 dark:text-orange-100">
                                {{ $customerStats['total'] > 0 ? number_format(\App\Models\Event::count() / $customerStats['total'], 1) : '0.0' }}
                            </p>
                            <div class="mt-2 text-xs text-orange-600 dark:text-orange-400">
                                {{ \App\Models\Event::count() }} total events
                            </div>
                        </div>
                        <div class="p-3 bg-orange-200 dark:bg-orange-800 rounded-full">
                            <x-heroicon-o-calendar class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row lg:gap-6 gap-6">
                {{-- Customer Growth Chart --}}
                <div class="flex-1 space-y-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-chart-bar class="w-4 h-4" />
                        Customer Growth (Last 6 Months)
                    </h3>
                    <div class="space-y-2">
                        @foreach ($growthData as $data)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ $data['month'] }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $data['count'] }} new
                                    </span>
                                    <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full"
                                            style="width: {{ max($growthData)['count'] > 0 ? ($data['count'] / max($growthData)['count']) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Top Customers by Events --}}
                <div class="flex-1 space-y-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-star class="w-4 h-4" />
                        Top Customers by Events
                    </h3>
                    <div class="space-y-2">
                        @forelse($topCustomers as $customer)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $customer->title }} {{ $customer->first_name }}
                                            {{ $customer->last_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $customer->email }}
                                        </p>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 rounded-full">
                                        {{ $customer->events_count }} events
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    <span>{{ $customer->phone }}</span>
                                    <span>{{ $customer->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No customers with events yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent Active Customers --}}
            <div class="space-y-3">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <x-heroicon-o-bolt class="w-4 h-4" />
                    Recently Active Customers
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($recentActiveCustomers as $customer)
                        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $customer->title }} {{ $customer->first_name }} {{ $customer->last_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $customer->events->count() }} events
                                    </p>
                                </div>
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span>{{ $customer->email }}</span>
                                <span>{{ $customer->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No active customers found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
