<div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700">
    <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @forelse ($activities as $activity)
                <div class="flex items-start space-x-3">
                    <div class="shrink-0 rounded-full p-2 bg-gray-100 dark:bg-zinc-700">
                        <flux:icon :name="$activity['icon']" class="w-4 h-4 text-gray-700 dark:text-gray-300" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $activity['action'] }}
                            <span class="font-medium">{{ $activity['item'] }}</span>
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $activity['time'] }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <flux:icon.clock class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <p class="text-gray-600 dark:text-gray-400">No recent activity found.</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Your recent actions will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
