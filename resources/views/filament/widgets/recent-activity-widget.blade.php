<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Recent Customers --}}
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-user-group class="w-4 h-4" />
                        Recent Customers
                    </h3>
                    <div class="space-y-2">
                        @forelse($recentCustomers as $customer)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $customer->title }} {{ $customer->first_name }}
                                            {{ $customer->last_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $customer->email }}
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-400">
                                        {{ $customer->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent customers</p>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Events --}}
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-calendar-days class="w-4 h-4" />
                        Recent Events
                    </h3>
                    <div class="space-y-2">
                        @forelse($recentEvents as $event)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $event->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $event->customer->full_name }} •
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-400">
                                        {{ $event->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent events</p>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Guests --}}
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-users class="w-4 h-4" />
                        Recent Guests
                    </h3>
                    <div class="space-y-2">
                        @forelse($recentGuests as $guest)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $guest->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $guest->customer->full_name }} • {{ $guest->events->count() }} events
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-400">
                                        {{ $guest->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent guests</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
