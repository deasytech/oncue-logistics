<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                    Upcoming Events
                </h2>
                <span
                    class="px-3 py-1 text-sm font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full">
                    {{ $totalUpcoming }} total
                </span>
            </div>

            @if (count($eventsByMonth) > 0)
                <div class="space-y-6">
                    @foreach ($eventsByMonth as $month => $events)
                        <div class="space-y-3">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $month }}</h3>
                            <div class="space-y-2">
                                @foreach ($events as $event)
                                    <div
                                        class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-l-4 border-indigo-500 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $event->name }}
                                                    </h4>
                                                    <span
                                                        class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full font-medium">
                                                        {{ \Carbon\Carbon::parse($event->event_date)->format('M d') }}
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="flex items-center gap-1">
                                                        <x-heroicon-o-user class="w-3 h-3" />
                                                        {{ $event->customer->full_name }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <x-heroicon-o-map-pin class="w-3 h-3" />
                                                        {{ $event->location }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <x-heroicon-o-users class="w-3 h-3" />
                                                        {{ $event->guests->count() }} guests
                                                    </span>
                                                </div>
                                                @if ($event->aso_ebi_color)
                                                    <div
                                                        class="flex items-center gap-1 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        <x-heroicon-o-swatch class="w-3 h-3" />
                                                        Aso Ebi: {{ $event->aso_ebi_color }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs text-gray-400 mb-1">
                                                    {{ \Carbon\Carbon::parse($event->event_date)->diffForHumans() }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $event->category?->name }} • {{ $event->subCategory?->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <x-heroicon-o-calendar class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                    <p class="text-gray-500 dark:text-gray-400">No upcoming events scheduled</p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
