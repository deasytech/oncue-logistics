<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-bolt" icon-color="gray" collapsible>
        <x-slot name="heading">Recent Activity</x-slot>
        <x-slot name="description">Latest customers, events, and guests added to the system</x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Recent Customers --}}
            <div>
                <h3
                    class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <x-heroicon-o-user-group class="w-3.5 h-3.5" />
                        Customers
                    </span>
                    <a href="{{ url('/admin/customers') }}"
                        class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors normal-case font-medium">
                        View all
                    </a>
                </h3>
                <div class="space-y-1.5">
                    @forelse ($recentCustomers as $customer)
                        <a href="{{ url('/admin/customers/' . $customer->id . '/view') }}"
                            class="block px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors group">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex items-center gap-2">
                                    <div
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold">
                                        {{ strtoupper(substr($customer->first_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                            {{ $customer->first_name }} {{ $customer->last_name }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                                            {{ $customer->email }}
                                        </p>
                                    </div>
                                </div>
                                <p class="shrink-0 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap mt-0.5">
                                    {{ $customer->created_at->diffForHumans(null, true) }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="py-6 text-center">
                            <x-heroicon-o-user-group class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600 mb-2" />
                            <p class="text-xs text-gray-400 dark:text-gray-500">No recent customers</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Events --}}
            <div>
                <h3
                    class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <x-heroicon-o-calendar-days class="w-3.5 h-3.5" />
                        Events
                    </span>
                    <a href="{{ url('/admin/events') }}"
                        class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors normal-case font-medium">
                        View all
                    </a>
                </h3>
                <div class="space-y-1.5">
                    @forelse ($recentEvents as $event)
                        <a href="{{ url('/admin/events/' . $event->id . '/view') }}"
                            class="block px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors group">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex items-center gap-2">
                                    <div
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400">
                                        <x-heroicon-m-calendar class="h-3.5 w-3.5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                            {{ $event->name }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                                            {{ $event->customer->full_name }}
                                            · {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                                <p class="shrink-0 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap mt-0.5">
                                    {{ $event->created_at->diffForHumans(null, true) }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="py-6 text-center">
                            <x-heroicon-o-calendar-days class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600 mb-2" />
                            <p class="text-xs text-gray-400 dark:text-gray-500">No recent events</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Guests --}}
            <div>
                <h3
                    class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <x-heroicon-o-users class="w-3.5 h-3.5" />
                        Guests
                    </span>
                    <a href="{{ url('/admin/guests') }}"
                        class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors normal-case font-medium">
                        View all
                    </a>
                </h3>
                <div class="space-y-1.5">
                    @forelse ($recentGuests as $guest)
                        <a href="{{ url('/admin/guests/' . $guest->id . '/view') }}"
                            class="block px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors group">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex items-center gap-2">
                                    <div
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                                        {{ strtoupper(substr($guest->first_name ?? $guest->full_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                            {{ $guest->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                                            {{ $guest->customer->full_name }}
                                            ·
                                            <x-filament::badge color="gray" size="sm">
                                                {{ $guest->events->count() }}
                                                {{ Str::plural('event', $guest->events->count()) }}
                                            </x-filament::badge>
                                        </p>
                                    </div>
                                </div>
                                <p class="shrink-0 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap mt-0.5">
                                    {{ $guest->created_at->diffForHumans(null, true) }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="py-6 text-center">
                            <x-heroicon-o-users class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600 mb-2" />
                            <p class="text-xs text-gray-400 dark:text-gray-500">No recent guests</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
