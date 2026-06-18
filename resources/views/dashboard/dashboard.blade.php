<x-layouts.app :title="__('Dashboard')">
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h1>
                    <p class="text-pink-100">Here's what's happening with your events today.</p>
                </div>
                <div class="hidden sm:block">
                    <flux:icon.calendar-days class="w-16 h-16 text-pink-200" />
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        @livewire('dashboard.dashboard-stats')

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Events -->
            <div class="lg:col-span-2">
                @livewire('dashboard.recent-events')
            </div>

            <!-- Quick Actions & Stats -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <flux:button variant="primary" class="w-full" href="{{ route('guests.create') }}" wire:navigate>
                            <flux:icon.user-plus class="w-4 h-4 mr-2" />
                            Add New Guest
                        </flux:button>
                        <flux:button variant="outline" class="w-full" href="{{ route('packages.list') }}" wire:navigate>
                            <flux:icon.shopping-bag class="w-4 h-4 mr-2" />
                            Browse Packages
                        </flux:button>
                        <flux:button variant="outline" class="w-full" href="{{ route('cart.summary') }}" wire:navigate>
                            <flux:icon.shopping-cart class="w-4 h-4 mr-2" />
                            View Cart
                        </flux:button>
                    </div>
                </div>

                <!-- Package Popularity -->
                {{-- <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Popular Packages</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach ([['name' => 'Premium Wedding Package', 'orders' => 45, 'percentage' => 85], ['name' => 'Corporate Event Package', 'orders' => 32, 'percentage' => 70], ['name' => 'Birthday Celebration Pack', 'orders' => 28, 'percentage' => 60], ['name' => 'Basic Event Package', 'orders' => 18, 'percentage' => 40]] as $package)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $package['name'] }}</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $package['orders'] }}
                                        orders</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-pink-500 to-purple-500 h-2 rounded-full transition-all duration-500"
                                        style="width: {{ $package['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div> --}}
            </div>
        </div>

        <!-- Recent Activity & Customer Insights -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activity -->
            @livewire('dashboard.recent-activity')

            <!-- Guest Engagement Stats -->
            @livewire('dashboard.guest-engagement-stats')
        </div>
    </div>

    <!-- Custom Styles -->
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

        .hover-card {
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</x-layouts.app>
