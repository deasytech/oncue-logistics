<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-calendar-days" icon-color="primary" collapsible>
        <x-slot name="heading">Upcoming Events</x-slot>
        <x-slot name="description">Events scheduled from today onwards</x-slot>
        <x-slot name="headerEnd">
            <x-filament::badge color="primary" size="sm">
                {{ $totalUpcoming }} {{ Str::plural('event', $totalUpcoming) }}
            </x-filament::badge>
        </x-slot>

        @if (count($eventsByMonth) > 0)
            <div class="space-y-5">
                @foreach ($eventsByMonth as $month => $events)
                    <div>
                        <p
                            class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <x-heroicon-m-calendar class="h-3.5 w-3.5" />
                            {{ $month }}
                            <x-filament::badge color="gray" size="sm">{{ count($events) }}</x-filament::badge>
                        </p>
                        <div class="space-y-2">
                            @foreach ($events as $event)
                                <a href="{{ url('/admin/events/' . $event->id . '/view') }}"
                                    class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-white/5 border-l-4 border-primary-500 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors group">
                                    {{-- Date badge --}}
                                    <div class="shrink-0 text-center min-w-[2.75rem]">
                                        <p
                                            class="text-xs font-bold text-primary-600 dark:text-primary-400 uppercase leading-tight">
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('M') }}
                                        </p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('d') }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('D') }}
                                        </p>
                                    </div>

                                    {{-- Event info --}}
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                            {{ $event->name }}
                                        </p>
                                        <div
                                            class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <x-heroicon-o-user class="w-3 h-3" />
                                                {{ $event->customer->full_name }}
                                            </span>
                                            @if ($event->location)
                                                <span class="flex items-center gap-1">
                                                    <x-heroicon-o-map-pin class="w-3 h-3" />
                                                    {{ $event->location }}
                                                </span>
                                            @endif
                                            <span class="flex items-center gap-1">
                                                <x-heroicon-o-users class="w-3 h-3" />
                                                {{ $event->guests->count() }}
                                                {{ Str::plural('guest', $event->guests->count()) }}
                                            </span>
                                        </div>
                                        @if ($event->category?->name || $event->subCategory?->name)
                                            <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                                                @if ($event->category?->name)
                                                    <x-filament::badge color="gray" size="sm">
                                                        {{ $event->category->name }}
                                                    </x-filament::badge>
                                                @endif
                                                @if ($event->subCategory?->name)
                                                    <x-filament::badge color="gray" size="sm">
                                                        {{ $event->subCategory->name }}
                                                    </x-filament::badge>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Countdown --}}
                                    <div class="shrink-0 text-right">
                                        @php
                                            $daysUntil = \Carbon\Carbon::today()->diffInDays(
                                                \Carbon\Carbon::parse($event->event_date),
                                                false,
                                            );
                                        @endphp
                                        @if ($daysUntil === 0)
                                            <x-filament::badge color="danger" size="sm">Today</x-filament::badge>
                                        @elseif ($daysUntil === 1)
                                            <x-filament::badge color="warning"
                                                size="sm">Tomorrow</x-filament::badge>
                                        @elseif ($daysUntil <= 7)
                                            <x-filament::badge color="warning" size="sm">
                                                {{ $daysUntil }}d
                                            </x-filament::badge>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ \Carbon\Carbon::parse($event->event_date)->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <x-heroicon-o-calendar class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" />
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No upcoming events</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Events scheduled in the future will appear
                    here.</p>
                <a href="{{ url('/admin/events/create') }}"
                    class="mt-4 inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                    <x-heroicon-o-plus class="h-3.5 w-3.5" />
                    Create an event
                </a>
            </div>
        @endif

        <x-slot name="footer">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Showing next {{ collect($eventsByMonth)->flatten()->count() }} of {{ $totalUpcoming }} events
                </p>
                <a href="{{ url('/admin/events') }}"
                    class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    View all events
                    <x-heroicon-m-arrow-right class="h-3.5 w-3.5" />
                </a>
            </div>
        </x-slot>
    </x-filament::section>
</x-filament-widgets::widget>
