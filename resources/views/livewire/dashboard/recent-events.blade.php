<div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700">
    <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Events</h2>
            <flux:button variant="primary" size="sm" href="{{ route('events.create') }}">
                <flux:icon.plus class="w-4 h-4 mr-2" />
                New Event
            </flux:button>
        </div>
    </div>
    <div class="p-6">
        @if ($recentEvents->isEmpty())
            <div class="text-center py-8">
                <flux:icon.calendar class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                <p class="text-gray-600 dark:text-gray-400">No upcoming events found.</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Create your first event to get started!</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($recentEvents as $event)
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-600 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-gradient-to-br from-pink-400 to-purple-500 p-3 rounded-lg">
                                <flux:icon.calendar class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $event['name'] }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($event['date'])->format('M d, Y') }} •
                                    {{ $event['guests'] }} guests
                                    @if ($event['location'])
                                        • {{ $event['location'] }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $event['status'] == 'confirmed'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                    : ($event['status'] == 'planning'
                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300') }}">
                                {{ ucfirst($event['status']) }}
                            </span>
                            <flux:button variant="ghost" size="sm" href="{{ route('events.list') }}" wire:navigate>
                                View
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
