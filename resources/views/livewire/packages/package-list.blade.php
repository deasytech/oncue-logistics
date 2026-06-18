<div class="relative min-h-screen">
    {{-- Page Content --}}
    <div class="pointer-events-none select-none blur-sm opacity-40">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            📦 Available Packages
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            Discover our curated collection of event packages
                        </p>
                    </div>

                    {{-- Search Section --}}
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.debounce.300ms="search" placeholder="Search packages..."
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 dark:focus:ring-pink-400 dark:focus:border-pink-400 transition-colors duration-200 text-sm" />
                    </div>
                </div>
            </div>

            {{-- Packages Grid --}}
            @if ($packages->isEmpty())
                {{-- Empty State --}}
                <div class="text-center py-20">
                    <div class="max-w-md mx-auto">
                        <div
                            class="w-24 h-24 mx-auto mb-6 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            No packages found
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            @if ($search)
                                Try adjusting your search terms or browse all packages.
                            @else
                                No packages are currently available. Check back soon!
                            @endif
                        </p>
                        @if ($search)
                            <button wire:click="$set('search', '')"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear search
                            </button>
                        @endif
                    </div>
                </div>
            @else
                {{-- Packages Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($packages as $package)
                        <div
                            class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-300 transform hover:-translate-y-1">
                            {{-- Image Section --}}
                            <div class="aspect-video bg-gray-100 dark:bg-gray-700 relative overflow-hidden">
                                @if ($package->cover_image ?? false)
                                    <img src="{{ asset('storage/' . $package->cover_image) }}"
                                        alt="{{ $package->name }}"
                                        class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div
                                        class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500">
                                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span class="text-sm">No image</span>
                                    </div>
                                @endif

                                {{-- Price Badge --}}
                                <div class="absolute top-3 right-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-md border border-gray-200 dark:border-gray-600">
                                        ₦{{ number_format($package->base_price, 2) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Content Section --}}
                            <div class="p-5">
                                <div class="mb-3">
                                    <h2
                                        class="font-bold text-lg text-gray-900 dark:text-white mb-1 line-clamp-1 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">
                                        {{ $package->name }}
                                    </h2>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 leading-relaxed">
                                        {{ $package->description }}
                                    </p>
                                </div>

                                {{-- Action Button --}}
                                <button wire:click="customize({{ $package->id }})"
                                    class="w-full bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 dark:from-pink-500 dark:to-pink-600 dark:hover:from-pink-600 dark:hover:to-pink-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 cursor-pointer">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Customize Package
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($packages->hasPages())
                    <div class="mt-12">
                        <div class="flex items-center justify-center">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 px-4 py-3">
                                {{ $packages->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Custom CSS for enhanced styling --}}
            <style>
                /* Custom scrollbar for dark mode */
                .dark ::-webkit-scrollbar {
                    width: 8px;
                }

                .dark ::-webkit-scrollbar-track {
                    background: #1f2937;
                }

                .dark ::-webkit-scrollbar-thumb {
                    background: #4b5563;
                    border-radius: 4px;
                }

                .dark ::-webkit-scrollbar-thumb:hover {
                    background: #6b7280;
                }

                /* Smooth transitions for all elements */
                * {
                    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
                }

                /* Enhanced focus states */
                button:focus-visible,
                input:focus-visible {
                    outline: 2px solid #6366f1;
                    outline-offset: 2px;
                }

                .dark button:focus-visible,
                .dark input:focus-visible {
                    outline-color: #818cf8;
                }
            </style>
        </div>
    </div>
    {{-- Coming Soon Overlay --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="text-center px-6">
            <div class="animate-pulse">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">
                    Coming Soon
                </h1>
                <p class="text-gray-200 text-sm sm:text-base max-w-md mx-auto">
                    We're putting the finishing touches on this feature. Stay tuned.
                </p>
            </div>
            <button
                onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'"
                class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-gray-900 font-semibold text-sm hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/70 cursor-pointer mt-9">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Go Back
            </button>
        </div>
    </div>
</div>
