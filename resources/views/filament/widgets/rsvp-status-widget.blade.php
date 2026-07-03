<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-chat-bubble-left-right" icon-color="warning" collapsible>
        <x-slot name="heading">RSVP Status</x-slot>
        <x-slot name="description">Guest response tracking across all events</x-slot>
        <x-slot name="headerEnd">
            <x-filament::badge
                color="{{ $rsvpStats['response_rate'] >= 75 ? 'success' : ($rsvpStats['response_rate'] >= 40 ? 'warning' : 'danger') }}"
                size="sm">
                {{ $rsvpStats['response_rate'] }}% responded
            </x-filament::badge>
        </x-slot>

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-5">
            <div
                class="rounded-xl p-4 bg-blue-50 dark:bg-blue-950/30 ring-1 ring-inset ring-blue-200/50 dark:ring-blue-800/30">
                <div class="flex items-center gap-2 mb-1">
                    <x-heroicon-o-users class="h-3.5 w-3.5 text-blue-500" />
                    <p class="text-xs font-medium text-blue-700 dark:text-blue-400 uppercase tracking-wide">Total
                        Guests</p>
                </div>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                    {{ number_format($rsvpStats['total_guests']) }}
                </p>
            </div>

            <div
                class="rounded-xl p-4 bg-emerald-50 dark:bg-emerald-950/30 ring-1 ring-inset ring-emerald-200/50 dark:ring-emerald-800/30">
                <div class="flex items-center gap-2 mb-1">
                    <x-heroicon-o-check-badge class="h-3.5 w-3.5 text-emerald-500" />
                    <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400 uppercase tracking-wide">
                        Responded</p>
                </div>
                <p class="text-2xl font-bold text-emerald-900 dark:text-emerald-100">
                    {{ number_format($rsvpStats['responded']) }}
                </p>
            </div>

            <div
                class="rounded-xl p-4 bg-amber-50 dark:bg-amber-950/30 ring-1 ring-inset ring-amber-200/50 dark:ring-amber-800/30">
                <div class="flex items-center gap-2 mb-1">
                    <x-heroicon-o-clock class="h-3.5 w-3.5 text-amber-500" />
                    <p class="text-xs font-medium text-amber-700 dark:text-amber-400 uppercase tracking-wide">Pending
                    </p>
                </div>
                <p class="text-2xl font-bold text-amber-900 dark:text-amber-100">
                    {{ number_format($rsvpStats['pending']) }}
                </p>
            </div>

            <div
                class="rounded-xl p-4 bg-purple-50 dark:bg-purple-950/30 ring-1 ring-inset ring-purple-200/50 dark:ring-purple-800/30">
                <div class="flex items-center gap-2 mb-1">
                    <x-heroicon-o-chart-pie class="h-3.5 w-3.5 text-purple-500" />
                    <p class="text-xs font-medium text-purple-700 dark:text-purple-400 uppercase tracking-wide">
                        Response Rate</p>
                </div>
                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                    {{ $rsvpStats['response_rate'] }}%
                </p>
            </div>
        </div>

        {{-- Response rate progress bar --}}
        <div class="mb-5">
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <span class="flex items-center gap-1">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                    Responded ({{ $rsvpStats['responded'] }})
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block h-2 w-2 rounded-full bg-amber-400"></span>
                    Pending ({{ $rsvpStats['pending'] }})
                </span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-white/10 rounded-full h-2.5 overflow-hidden">
                <div class="h-2.5 rounded-full transition-all duration-700
                    {{ $rsvpStats['response_rate'] >= 75 ? 'bg-emerald-500' : ($rsvpStats['response_rate'] >= 40 ? 'bg-amber-400' : 'bg-red-500') }}"
                    style="width: {{ $rsvpStats['response_rate'] }}%">
                </div>
            </div>
        </div>

        {{-- Recent RSVP responses --}}
        <div class="mb-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-1.5">
                <x-heroicon-o-bell class="w-4 h-4 text-gray-400" />
                Recent Responses
            </h3>
            <div class="space-y-1.5">
                @forelse ($recentResponses as $response)
                    <div
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $response->first_name }} {{ $response->last_name }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $response->event_name }}
                            </p>
                        </div>
                        <x-filament::badge
                            color="{{ $response->attendance_status === 'attending' ? 'success' : ($response->attendance_status === 'not_attending' ? 'danger' : 'gray') }}"
                            size="sm" class="ml-3 shrink-0">
                            {{ ucfirst(str_replace('_', ' ', $response->attendance_status)) }}
                        </x-filament::badge>
                    </div>
                @empty
                    <div class="py-6 text-center">
                        <x-heroicon-o-chat-bubble-left-ellipsis
                            class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600 mb-2" />
                        <p class="text-sm text-gray-400 dark:text-gray-500">No RSVP responses yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Upcoming events RSVP --}}
        @if ($upcomingEvents->isNotEmpty())
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-1.5">
                    <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                    Events Awaiting Responses
                </h3>
                <div class="space-y-2">
                    @foreach ($upcomingEvents as $event)
                        <div class="px-3 py-3 rounded-lg bg-gray-50 dark:bg-white/5">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $event->name }}
                                </p>
                                <div class="ml-2 flex items-center gap-1.5 shrink-0">
                                    <x-filament::badge color="gray" size="sm">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('M d') }}
                                    </x-filament::badge>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-gray-200 dark:bg-white/10 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500"
                                        style="width: {{ $event->total_guests > 0 ? ($event->responded_guests / $event->total_guests) * 100 : 0 }}%">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0">
                                    {{ $event->responded_guests }}/{{ $event->total_guests }}
                                    <span class="text-gray-400 dark:text-gray-500">responded</span>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <x-slot name="footer">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $rsvpStats['pending'] }} guests still pending
                </p>
                <a href="{{ url('/admin/guests') }}"
                    class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    Manage guests
                    <x-heroicon-m-arrow-right class="h-3.5 w-3.5" />
                </a>
            </div>
        </x-slot>
    </x-filament::section>
</x-filament-widgets::widget>
