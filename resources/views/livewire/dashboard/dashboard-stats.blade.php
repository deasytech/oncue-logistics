<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Total Events -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Events</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalEvents }}</p>
                <p
                    class="text-sm {{ $eventsGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-2">
                    {{ $eventsGrowth >= 0 ? '+' : '' }}{{ $eventsGrowth }}% from last month
                </p>
            </div>
            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                <flux:icon.calendar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
        </div>
    </div>

    <!-- Total Guests -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Guests</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalGuests }}</p>
                <p
                    class="text-sm {{ $guestsGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-2">
                    {{ $guestsGrowth >= 0 ? '+' : '' }}{{ $guestsGrowth }}% from last month
                </p>
            </div>
            <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                <flux:icon.user-group class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
        </div>
    </div>

    <!-- Active Packages -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Packages</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $activePackages }}</p>
                <p class="text-sm text-blue-600 dark:text-blue-400 mt-2">Available for booking</p>
            </div>
            <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                <flux:icon.archive-box class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
        </div>
    </div>

    <!-- Fabric Payments Received -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-neutral-200 dark:border-neutral-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Fabric Payments Received</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">₦{{ number_format($totalRevenue, 2) }}
                </p>
                <p
                    class="text-sm {{ $revenueGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-2">
                    {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}% from last month
                </p>
            </div>
            <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-lg">
                <flux:icon.briefcase class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
            </div>
        </div>
    </div>
</div>
