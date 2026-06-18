<div>
    <!-- Welcome Section - Hero Banner -->
    <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-xl p-6 text-white mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Edit Guest</h1>
                <p class="text-orange-100">Update guest details and information</p>
            </div>
            <div class="hidden sm:block">
                <svg class="w-16 h-16 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10">
                    </path>
                </svg>
            </div>
        </div>
    </div>

    @include('livewire.guests._form', ['isEdit' => true])

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
