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
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Guest Management</h1>
                    <p class="text-purple-100">Manage your event guests and invitations</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-16 h-16 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                {{-- <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your Guests</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manage your guest collection</p> --}}
            </div>
            <div class="flex gap-3">
                <a href="{{ route('guests.import') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                        </path>
                    </svg>
                    Import Guests
                </a>
                <a href="{{ route('guests.create') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Guest
                </a>
            </div>
        </div>

        <!-- Main Content Card -->
        <div
            class="relative h-full flex-1 overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 shadow-sm">
            <div class="p-8">
                <!-- Search Bar -->
                <div class="mb-8 flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Search guests by name, email, or phone..."
                                class="w-full pl-12 pr-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Guests Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-8 py-4 font-semibold">
                                    <button wire:click="sortBy('last_name')"
                                        class="flex items-center hover:text-gray-900 dark:hover:text-white transition-colors">
                                        Guest Name
                                        @if ($sortField === 'last_name')
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-8 py-4 font-semibold">Email</th>
                                <th scope="col" class="px-8 py-4 font-semibold">Phone</th>
                                <th scope="col" class="px-8 py-4 font-semibold text-center">Events</th>
                                <th scope="col" class="px-8 py-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                            @forelse($guests as $guest)
                                <tr wire:key="guest-{{ $guest->id }}"
                                    class="bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition-colors duration-200">
                                    <td class="px-8 py-6">
                                        <div class="font-semibold text-gray-900 dark:text-white text-base">
                                            {{ $guest->full_name }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-gray-900 dark:text-white font-medium">
                                            {{ $guest->email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-gray-900 dark:text-white font-medium">
                                            {{ $guest->phone ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span
                                            class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300">
                                            {{ $guest->events->count() }}
                                            event{{ $guest->events->count() !== 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('guests.edit', $guest) }}" wire:navigate
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400 hover:bg-pink-100 dark:hover:bg-pink-900/30 transition-all duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <button wire:click="confirmDelete({{ $guest->id }})"
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
                                    <td colspan="6" class="px-8 py-16 text-center">
                                        <div class="relative">
                                            <div
                                                class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-700 dark:to-zinc-600 flex items-center justify-center">
                                                <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                            No Guests Found
                                        </h3>
                                        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                                            Start by adding your first guest to manage your event guest lists.
                                        </p>
                                        <a href="{{ route('guests.create') }}" wire:navigate
                                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Add Your First Guest
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($guests->hasPages())
                    <div
                        class="mt-8 flex items-center justify-between border-t border-gray-100 dark:border-zinc-700 pt-6">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">{{ $guests->total() }}</span> total guests
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $guests->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Account Restriction Modal -->
    @if ($hasInactiveEvents)
        <div class="fixed inset-0 z-[60] flex items-center justify-center px-4 bg-black/60 backdrop-blur-sm">
            <div
                class="bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-md w-full mx-4 p-8 text-center border border-gray-100 dark:border-zinc-800">
                <div
                    class="w-20 h-20 mx-auto mb-5 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Access Restricted</h2>

                <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                    One or more of your events are currently <span class="font-semibold text-red-600">inactive</span>.
                    Guest Management will be activated once payment is received and confirmed.
                </p>

                <div
                    class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-6 text-left">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-amber-700 dark:text-amber-300">
                            Please contact the administrator to lift this restriction and activate your event.
                        </p>
                    </div>
                </div>

                <a href="mailto:info@oncuelogistics.com"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Contact Admin
                </a>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-data="{ open: @entangle('confirmingDelete') }" x-show="open" x-transition.opacity.duration.300ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
        style="display: none;">
        <div x-transition.duration.300ms.scale.origin.center
            class="bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-md w-full mx-4 p-8 text-center relative border border-gray-100 dark:border-zinc-800">
            <!-- Animated Warning Icon -->
            <div x-data="{ bounce: true }" x-init="setInterval(() => bounce = !bounce, 1200)"
                class="w-20 h-20 mx-auto mb-5 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center"
                :class="{ 'animate-bounce': bounce }">
                <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                </svg>
            </div>

            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Delete Guest?</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Are you sure you want to permanently delete this guest?
                <br>This action <span class="text-red-600 font-semibold">cannot be undone</span>.
            </p>
            <div class="flex justify-center gap-4">
                <button wire:click="cancelDelete"
                    class="px-6 py-2.5 rounded-xl bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 font-medium">
                    Cancel
                </button>

                <button wire:click="deleteGuest"
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
