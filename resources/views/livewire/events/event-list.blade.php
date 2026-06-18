<div>
    @if (session()->has('message'))
        <div
            class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Welcome Section - Hero Banner -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Events Management</h1>
                    <p class="text-pink-100">Organize and manage all your events in one place</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-16 h-16 text-pink-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your Events</h2>
                <p class="text-gray-600 dark:text-gray-400">Manage your event collection</p>
            </div>
            <a href="{{ route('events.create') }}" wire:navigate
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New Event
            </a>
        </div>

        <!-- Main Content Card -->
        <div
            class="relative h-full flex-1 overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
            <div class="p-8">
                <!-- Search and Filter Bar -->
                <div class="mb-8 flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Search events by name or location..."
                                class="w-full pl-12 pr-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Events Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-8 py-4 font-semibold">
                                    <button wire:click="sortBy('name')"
                                        class="flex items-center hover:text-gray-900 dark:hover:text-white transition-colors uppercase">
                                        Event
                                        @if ($sortField === 'name')
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-8 py-4 font-semibold text-left">Event Date</th>
                                <th scope="col" class="px-8 py-4 font-semibold text-left">Event Category</th>
                                <th scope="col" class="px-8 py-4 font-semibold text-left">Location</th>
                                <th scope="col" class="px-8 py-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                            @forelse($events as $event)
                                <tr wire:key="event-{{ $event->id }}"
                                    class="bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition-colors duration-200">
                                    <td class="px-8 py-6">
                                        <div class="font-semibold text-gray-900 dark:text-white text-base">
                                            {{ $event->name }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-gray-900 dark:text-white font-medium">
                                            {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('j F, Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-gray-900 dark:text-white font-medium">
                                            {{ $event->category->name ?? 'N/A' }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            [{{ $event->subcategory->name ?? ($event->custom_subcategory ?? 'N/A') }}]</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-gray-900 dark:text-white font-medium">
                                            {{ $event->location ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('events.edit', $event) }}" wire:navigate
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400 hover:bg-pink-100 dark:hover:bg-pink-900/30 transition-all duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <button wire:click="confirmDelete({{ $event->id }})"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center">
                                        <div class="relative">
                                            <div
                                                class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-700 dark:to-zinc-600 flex items-center justify-center">
                                                <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No
                                            Events found</h3>
                                        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">Get started
                                            by creating your first event to manage your event relationships.</p>
                                        <a href="{{ route('events.create') }}" wire:navigate
                                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Create Your First Event
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($events->hasPages())
                    <div
                        class="mt-8 flex items-center justify-between border-t border-gray-100 dark:border-zinc-700 pt-6">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">{{ $events->total() }}</span> total events
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $events->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Custom Styles for Hero Banner -->
    <style>
        .bg-gradient-to-r {
            background-size: 200% 200%;
            animation: gradient 3s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ open: @entangle('confirmingDelete') }" x-show="open" x-transition.opacity.duration.300ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
        style="display: none;">
        <div x-transition.duration.300ms.scale.origin.center
            class="bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-md w-full mx-4 p-8 text-center relative border border-gray-100 dark:border-zinc-800">
            <!-- Animated Red Warning Icon -->
            <div x-data="{ bounce: true }" x-init="setInterval(() => bounce = !bounce, 1200)"
                class="w-20 h-20 mx-auto mb-5 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center"
                :class="{ 'animate-bounce': bounce }">
                <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                </svg>
            </div>

            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Delete Event?</h2>

            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Are you sure you want to permanently delete this event?
                <br>This action <span class="text-red-600 font-semibold">cannot be undone</span>.
            </p>
            <div class="flex justify-center gap-4">
                <button wire:click="cancelDelete"
                    class="px-6 py-2.5 rounded-xl bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 font-medium">
                    Changed My Mind
                </button>

                <button wire:click="deleteEvent"
                    class="px-6 py-2.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-all duration-200 font-semibold shadow-md hover:shadow-lg">
                    Yes, Delete
                </button>
            </div>
            <button wire:click="cancelDelete"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
