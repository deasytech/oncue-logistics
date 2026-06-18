<div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700">
    <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Guest Response Overview</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Track RSVP engagement and fabric payment progress.</p>
    </div>

    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-lg bg-blue-50 dark:bg-blue-950/30 p-4 border border-blue-100 dark:border-blue-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300">RSVP Sent</p>
                    <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-1">{{ $totalRsvpSent }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">Invitations delivered to guests</p>
                </div>
                <flux:icon.paper-airplane class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
        </div>

        <div class="rounded-lg bg-green-50 dark:bg-green-950/30 p-4 border border-green-100 dark:border-green-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">Accepted to Attend</p>
                    <p class="text-3xl font-bold text-green-900 dark:text-green-100 mt-1">{{ $totalAccepted }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-2">Guests who confirmed attendance</p>
                </div>
                <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
        </div>

        <div class="rounded-lg bg-red-50 dark:bg-red-950/30 p-4 border border-red-100 dark:border-red-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-red-700 dark:text-red-300">Declined</p>
                    <p class="text-3xl font-bold text-red-900 dark:text-red-100 mt-1">{{ $totalDeclined }}</p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-2">Guests who declined the invite</p>
                </div>
                <flux:icon.x-circle class="w-6 h-6 text-red-600 dark:text-red-400" />
            </div>
        </div>

        <div class="rounded-lg bg-amber-50 dark:bg-amber-950/30 p-4 border border-amber-100 dark:border-amber-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-amber-700 dark:text-amber-300">Fabric Paid</p>
                    <p class="text-3xl font-bold text-amber-900 dark:text-amber-100 mt-1">
                        {{ $totalFabricPaid }}/{{ $totalGuestsInvited }}</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">{{ $this->fabricPaidPercentage }}% of
                        invited guests have paid</p>
                </div>
                <flux:icon.currency-dollar class="w-6 h-6 text-amber-600 dark:text-amber-400" />
            </div>
        </div>
    </div>
</div>
