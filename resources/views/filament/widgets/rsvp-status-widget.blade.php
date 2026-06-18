<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">RSVP Status Overview</h2>

            {{-- RSVP Statistics --}}
            <div class="flex flex-col md:flex-row gap-4">
                <div
                    class="w-full p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-600 dark:text-blue-400">Total Guests</p>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                {{ $rsvpStats['total_guests'] }}</p>
                        </div>
                        <x-heroicon-o-users class="w-8 h-8 text-blue-500" />
                    </div>
                </div>

                <div
                    class="w-full p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-600 dark:text-green-400">Responded</p>
                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                                {{ $rsvpStats['responded'] }}</p>
                        </div>
                        <x-heroicon-o-check-circle class="w-8 h-8 text-green-500" />
                    </div>
                </div>

                <div
                    class="w-full p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-yellow-600 dark:text-yellow-400">Pending</p>
                            <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">
                                {{ $rsvpStats['pending'] }}</p>
                        </div>
                        <x-heroicon-o-clock class="w-8 h-8 text-yellow-500" />
                    </div>
                </div>

                <div
                    class="w-full p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-purple-600 dark:text-purple-400">Response Rate</p>
                            <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                                {{ $rsvpStats['response_rate'] }}%</p>
                        </div>
                        <x-heroicon-o-chart-bar class="w-8 h-8 text-purple-500" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent RSVP Responses --}}
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-bell class="w-4 h-4" />
                        Recent Responses
                    </h3>
                    <div class="space-y-2">
                        @forelse($recentResponses as $response)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $response->first_name }} {{ $response->last_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $response->event_name }}
                                        </p>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs rounded-full 
                                        @if ($response->attendance_status === 'attending') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($response->attendance_status === 'not_attending') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $response->attendance_status)) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    <span>{{ $response->customer_first_name }}
                                        {{ $response->customer_last_name }}</span>
                                    <span>{{ \Carbon\Carbon::parse($response->rsvp_responded_at)->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent RSVP responses</p>
                        @endforelse
                    </div>
                </div>

                {{-- Upcoming Events with RSVP Status --}}
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-4 h-4" />
                        Upcoming Events RSVP
                    </h3>
                    <div class="space-y-2">
                        @forelse($upcomingEvents as $event)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
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
                                        {{ \Carbon\Carbon::parse($event->event_date)->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 text-xs">
                                        <span class="text-gray-600 dark:text-gray-400">
                                            Total: {{ $event->total_guests }}
                                        </span>
                                        <span class="text-green-600 dark:text-green-400">
                                            Responded: {{ $event->responded_guests }}
                                        </span>
                                    </div>
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full"
                                            style="width: {{ $event->total_guests > 0 ? ($event->responded_guests / $event->total_guests) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming events with guests</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
