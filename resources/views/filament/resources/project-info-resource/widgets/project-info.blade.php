<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-wrap items-center justify-between gap-4">
            {{-- Brand & Version --}}
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-500/10 dark:bg-primary-400/10">
                    <x-heroicon-o-building-office-2 class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">Oncue Logistics</span>
                        <x-filament::badge color="primary" size="sm">v0.01</x-filament::badge>
                        <x-filament::badge color="success" size="sm">
                            <x-slot name="icon">heroicon-m-signal</x-slot>
                            Live
                        </x-filament::badge>
                    </div>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        Event logistics & guest management platform
                    </p>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ url('/admin/customers') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/15 transition-colors">
                    <x-heroicon-o-user-group class="h-3.5 w-3.5" />
                    Customers
                </a>
                <a href="{{ url('/admin/events') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/15 transition-colors">
                    <x-heroicon-o-calendar-days class="h-3.5 w-3.5" />
                    Events
                </a>
                <a href="{{ url('/admin/guests') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/15 transition-colors">
                    <x-heroicon-o-list-bullet class="h-3.5 w-3.5" />
                    Guests
                </a>
                <a href="{{ url('/admin/invoices') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/15 transition-colors">
                    <x-heroicon-o-document-text class="h-3.5 w-3.5" />
                    Invoices
                </a>
                <a href="{{ url('/admin/events/create') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 transition-colors shadow-sm">
                    <x-heroicon-o-plus class="h-3.5 w-3.5" />
                    New Event
                </a>
                <a href="{{ url('/admin/users') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/15 transition-colors">
                    <x-heroicon-o-users class="h-3.5 w-3.5" />
                    Users
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
