<div>
    <!-- Welcome Section - Hero Banner -->
    <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl p-6 text-white mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Create New Guest</h1>
                <p class="text-green-100">Add a new guest to your event list</p>
            </div>
            <div class="hidden sm:block">
                <svg class="w-16 h-16 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
        </div>
    </div>

    @include('livewire.guests._form', ['isEdit' => false])

    <!-- Custom Styles for Hero Banner -->
    @push('styles')
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
    @endpush
</div>
