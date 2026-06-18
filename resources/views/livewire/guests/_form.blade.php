<div title="{{ $isEdit ? __('Edit Guest') : __('Add Guest') }}">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                {{-- <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $isEdit ? 'Edit Guest' : 'Add Guest' }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $isEdit ? 'Update guest information' : 'Add a new guest to your event list' }}
                </p> --}}
            </div>

            <a href="{{ route('guests.list') }}" wire:navigate
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700 font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Guests
            </a>
        </div>

        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800">
            <div class="p-6">

                @if (session()->has('message'))
                    <div
                        class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900 dark:border-green-700 dark:text-green-300">
                        {{ session('message') }}
                    </div>
                @endif

                @if ($showNotificationConfirm)
                    <div
                        class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-5 dark:border-amber-700 dark:bg-amber-950/40">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">
                                <flux:icon.exclamation-triangle class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-amber-900 dark:text-amber-200">Send guest
                                    notification?</h3>
                                <p class="mt-1 text-sm text-amber-800 dark:text-amber-300">
                                    Do you want to send a notification to this guest after saving?
                                </p>

                                <div class="mt-4 flex flex-wrap gap-3">
                                    <button type="button" wire:click="confirmSaveWithNotifications"
                                        wire:loading.attr="disabled" wire:target="confirmSaveWithNotifications"
                                        class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                        <span wire:loading.remove wire:target="confirmSaveWithNotifications">Yes!
                                            Send</span>
                                        <span wire:loading wire:target="confirmSaveWithNotifications"
                                            class="inline-flex items-center gap-2">
                                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                            </svg>
                                            Sending...
                                        </span>
                                    </button>
                                    <button type="button" wire:click="confirmSaveWithoutNotifications"
                                        wire:loading.attr="disabled" wire:target="confirmSaveWithoutNotifications"
                                        class="inline-flex items-center rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-zinc-600 dark:hover:bg-zinc-500">
                                        <span wire:loading.remove wire:target="confirmSaveWithoutNotifications">No! Do
                                            not send</span>
                                        <span wire:loading wire:target="confirmSaveWithoutNotifications"
                                            class="inline-flex items-center gap-2">
                                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                            </svg>
                                            Saving...
                                        </span>
                                    </button>
                                    <button type="button" wire:click="cancelNotificationPrompt"
                                        class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-zinc-700">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Hidden Customer ID -->
                    <input type="hidden" wire:model="customer_id">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Guest Name -->
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Guest Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="name" id="name"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                placeholder="Enter guest name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number
                            </label>
                            <input type="text" wire:model="phone" id="phone"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                placeholder="Enter phone number">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email
                            </label>
                            <input type="email" wire:model="email" id="email"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                placeholder="Enter guest email">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- RSVP Status -->
                        <div>
                            <label for="rsvp_status"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                RSVP Status
                            </label>
                            <select wire:model="rsvp_status" id="rsvp_status"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select RSVP Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="declined">Declined</option>
                            </select>
                            @error('rsvp_status')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Selection -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Attach to Events
                            </label>
                            <div
                                class="space-y-2 max-h-40 overflow-y-auto border rounded-lg p-3 bg-white dark:bg-zinc-700">
                                @if (count($customerEvents) > 0)
                                    @foreach ($customerEvents as $event)
                                        <label class="flex items-center space-x-3 cursor-pointer">
                                            <input type="checkbox" wire:model="selectedEvents"
                                                value="{{ $event->id }}"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                                {{ $event->name }} -
                                                <span class="text-gray-500 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        No events found. Create an event first to attach guests.
                                    </p>
                                @endif
                            </div>
                            @error('selectedEvents')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @if (count($selectedEvents) > 0)
                                <p class="mt-1 text-sm text-green-600 dark:text-green-400">
                                    Selected: {{ count($selectedEvents) }} event(s)
                                </p>
                            @endif
                        </div>

                        <!-- Address with Google Places Autocomplete + Geolocation -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <x-google-places-input name="address" label="Address"
                                placeholder="Start typing to search for an address..." :value="$address"
                                wireModel="address" :required="true" />
                            @error('address')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            <!-- Required checkbox to trigger geolocation generation -->
                            <div class="mt-3 flex items-start space-x-2">
                                <input id="geolocation_required" type="checkbox" required
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    aria-describedby="geolocation-help">
                                <div class="text-sm">
                                    <label for="geolocation_required"
                                        class="font-medium text-gray-700 dark:text-gray-300">
                                        Generate coordinates from selected address (required) <span
                                            class="text-sm text-red-600 dark:text-red-400">*</span>
                                    </label>
                                    <p id="geolocation-help" class="text-xs text-gray-500 dark:text-gray-400">
                                        Check this to generate latitude & longitude from the address. This will trigger
                                        the geolocation routine and dispatch a "geolocation-ready" event with the
                                        coordinates so any delivery API integration can include them.
                                    </p>
                                </div>
                            </div>

                            <!-- Manual trigger and status -->
                            <div class="mt-2 flex items-center gap-3">
                                <button id="geocode_btn" type="button"
                                    class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-md text-sm text-gray-700"
                                    disabled>
                                    Generate coordinates
                                </button>
                                <span id="geocode_status" class="text-sm text-gray-500 dark:text-gray-400">No
                                    coordinates generated</span>
                            </div>

                            <!-- Hidden fields to store coordinates for client-side consumers -->
                            <input type="hidden" id="latitude_store" name="latitude_store" />
                            <input type="hidden" id="longitude_store" name="longitude_store" />

                            <script>
                                (function() {
                                    // Helper to find the address input rendered by x-google-places-input
                                    function findAddressInput() {
                                        // prefer input[name="address"]
                                        return document.querySelector(
                                            'input[name="address"], input#address, input[placeholder*="Start typing"]');
                                    }

                                    let geocodeBtn = null;
                                    let geolocationCheckbox = null;
                                    let statusEl = null;
                                    let latInput = null;
                                    let lngInput = null;

                                    function setStatus(text, isError = false) {
                                        if (!statusEl) return;
                                        statusEl.textContent = text;
                                        statusEl.classList.toggle('text-red-600', isError);
                                    }

                                    async function geocodeAddress(address) {
                                        if (!address || address.trim().length === 0) {
                                            setStatus('No address to geocode', true);
                                            return null;
                                        }

                                        // Try to use Google Maps JS Geocoder if available
                                        if (window.google && window.google.maps && window.google.maps.Geocoder) {
                                            return new Promise((resolve, reject) => {
                                                try {
                                                    const geocoder = new google.maps.Geocoder();
                                                    geocoder.geocode({
                                                        address: address
                                                    }, (results, status) => {
                                                        if (status === 'OK' && results && results[0] && results[0]
                                                            .geometry && results[0].geometry.location) {
                                                            const loc = results[0].geometry.location;
                                                            resolve({
                                                                latitude: loc.lat(),
                                                                longitude: loc.lng(),
                                                                raw: results[0]
                                                            });
                                                        } else {
                                                            reject(new Error('Geocoding failed: ' + status));
                                                        }
                                                    });
                                                } catch (e) {
                                                    reject(e);
                                                }
                                            });
                                        }

                                        // Fallback: try calling Google Geocode REST API (requires API key)
                                        // NOTE: Replace YOUR_API_KEY or provide server-side geocoding if needed.
                                        if (window.fetch) {
                                            try {
                                                const PLACES_API_KEY = @json(config('services.google.places_api_key', ''));
                                                if (!PLACES_API_KEY) {
                                                    // No client key available — request server-side geocoding immediately
                                                    if (window.Livewire && typeof Livewire.emit === 'function') {
                                                        try {
                                                            Livewire.emit('request-server-geocode', {
                                                                address: address
                                                            });
                                                            setStatus(
                                                                'Client geocoding unavailable — requested server geocode. Waiting for results...'
                                                            );
                                                            return null;
                                                        } catch (e) {
                                                            // fallthrough to error
                                                        }
                                                    }
                                                    throw new Error('No client API key for geocoding');
                                                }
                                                const url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' +
                                                    encodeURIComponent(address) + '&key=' + encodeURIComponent(PLACES_API_KEY);
                                                const res = await fetch(url);
                                                const data = await res.json();
                                                if (data.status === 'OK' && data.results && data.results[0]) {
                                                    const loc = data.results[0].geometry.location;
                                                    return {
                                                        latitude: loc.lat,
                                                        longitude: loc.lng,
                                                        raw: data.results[0]
                                                    };
                                                } else {
                                                    // If client-side REST is denied (e.g., key restricted), request server-side geocode
                                                    if (data.status === 'REQUEST_DENIED' || data.status === 'INVALID_REQUEST' || data
                                                        .status === 'OVER_QUERY_LIMIT') {
                                                        // Ask Livewire server to perform geocoding using server API key
                                                        if (window.Livewire && typeof Livewire.emit === 'function') {
                                                            try {
                                                                Livewire.emit('request-server-geocode', {
                                                                    address: address
                                                                });
                                                                setStatus(
                                                                    'Client geocoding denied — requested server geocode. Waiting for results...'
                                                                );
                                                                return null;
                                                            } catch (e) {
                                                                // fallthrough to error
                                                            }
                                                        }
                                                    }
                                                    throw new Error('Geocoding REST failed: ' + (data.status || 'no-result'));
                                                }
                                            } catch (e) {
                                                throw e;
                                            }
                                        }

                                        throw new Error('No geocoding method available on this client.');
                                    }

                                    function init() {
                                        geocodeBtn = document.getElementById('geocode_btn');
                                        geolocationCheckbox = document.getElementById('geolocation_required');
                                        statusEl = document.getElementById('geocode_status');
                                        latInput = document.getElementById('latitude_store');
                                        lngInput = document.getElementById('longitude_store');

                                        const addressInput = findAddressInput();

                                        // Enable button only after checkbox is checked
                                        function updateControls() {
                                            const checked = geolocationCheckbox && geolocationCheckbox.checked;
                                            if (geocodeBtn) geocodeBtn.disabled = !checked;
                                        }

                                        if (geolocationCheckbox) {
                                            geolocationCheckbox.addEventListener('change', updateControls);
                                        }

                                        if (geocodeBtn) {
                                            geocodeBtn.addEventListener('click', async function() {
                                                const address = addressInput ? addressInput.value : '';
                                                setStatus('Generating coordinates...');
                                                try {
                                                    const result = await geocodeAddress(address);
                                                    if (result) {
                                                        latInput.value = result.latitude;
                                                        lngInput.value = result.longitude;
                                                        setStatus('Coordinates generated: ' + result.latitude.toFixed(6) + ', ' +
                                                            result.longitude.toFixed(6));
                                                        // Dispatch a custom event for any delivery API consumer to pick up coordinates
                                                        window.dispatchEvent(new CustomEvent('geolocation-ready', {
                                                            detail: {
                                                                address: address,
                                                                latitude: result.latitude,
                                                                longitude: result.longitude,
                                                                raw: result.raw
                                                            }
                                                        }));
                                                        // Also emit a Livewire event in case backend listens
                                                        if (window.Livewire && typeof Livewire.emit === 'function') {
                                                            try {
                                                                Livewire.emit('address-geocoded', {
                                                                    field: 'address',
                                                                    value: address,
                                                                    latitude: result.latitude,
                                                                    longitude: result.longitude
                                                                });
                                                            } catch (e) {
                                                                // ignore emit errors
                                                            }
                                                        }
                                                    }
                                                } catch (err) {
                                                    console.error(err);
                                                    setStatus('Failed to generate coordinates: ' + (err.message || 'unknown'),
                                                        true);
                                                }
                                            });
                                        }

                                        // If an address value is updated by the places input, optionally auto-run geocode when checkbox is checked
                                        if (addressInput) {
                                            addressInput.addEventListener('change', function() {
                                                // If user has checked checkbox, automatically trigger generation
                                                if (geolocationCheckbox && geolocationCheckbox.checked) {
                                                    geocodeBtn && geocodeBtn.click();
                                                } else {
                                                    setStatus(
                                                        'Address changed — check the box and click Generate coordinates to produce lat/lng.'
                                                    );
                                                }
                                            });

                                            // Some autocomplete widgets update via 'input' event
                                            addressInput.addEventListener('input', function() {
                                                if (geolocationCheckbox && geolocationCheckbox.checked) {
                                                    // Do not auto-fire on every keystroke; only when input likely complete.
                                                    // No-op here; user can click Generate.
                                                }
                                            });

                                            // When the places component dispatches a place-selected event it usually includes coordinates.
                                            // Use those directly to avoid REST geocode restrictions.
                                            addressInput.addEventListener('place-selected', function(e) {
                                                try {
                                                    const detail = e?.detail || {};
                                                    const place = detail.place || {};
                                                    const location = place.location || place.geometry || place.geometry?.location ||
                                                        null;
                                                    // Support variations: {lat,lng} or {latitude,longitude}
                                                    let lat = null;
                                                    let lng = null;
                                                    if (location) {
                                                        lat = location.lat ?? location.latitude ?? (location.lat ? location.lat : null);
                                                        lng = location.lng ?? location.longitude ?? (location.lng ? location.lng :
                                                            null);
                                                    } else if (place.location && typeof place.location === 'object') {
                                                        lat = place.location.lat ?? place.location.latitude;
                                                        lng = place.location.lng ?? place.location.longitude;
                                                    }

                                                    if (lat !== null && lng !== null && lat !== undefined && lng !== undefined) {
                                                        latInput.value = Number(lat);
                                                        lngInput.value = Number(lng);
                                                        setStatus('Coordinates from selection: ' + Number(lat).toFixed(6) + ', ' +
                                                            Number(lng).toFixed(6));
                                                        // Notify consumers
                                                        window.dispatchEvent(new CustomEvent('geolocation-ready', {
                                                            detail: {
                                                                address: addressInput.value,
                                                                latitude: Number(lat),
                                                                longitude: Number(lng),
                                                                raw: detail
                                                            }
                                                        }));
                                                        if (window.Livewire && typeof Livewire.emit === 'function') {
                                                            try {
                                                                Livewire.emit('address-geocoded', {
                                                                    field: 'address',
                                                                    value: addressInput.value,
                                                                    latitude: Number(lat),
                                                                    longitude: Number(lng)
                                                                });
                                                            } catch (e) {
                                                                // ignore
                                                            }
                                                        }
                                                    }
                                                } catch (err) {
                                                    // ignore
                                                }
                                            });
                                        }
                                    }

                                    // Initialize when DOM is ready
                                    // Listen for server-side geocode responses
                                    if (window.Livewire && typeof Livewire.on === 'function') {
                                        Livewire.on('address-geocoded', function(data) {
                                            try {
                                                if (data && (data.latitude !== undefined) && (data.longitude !== undefined)) {
                                                    latInput.value = data.latitude;
                                                    lngInput.value = data.longitude;
                                                    setStatus('Coordinates received: ' + Number(data.latitude).toFixed(6) + ', ' +
                                                        Number(data.longitude).toFixed(6));
                                                    // Dispatch same custom event so delivery API consumers behave consistently
                                                    window.dispatchEvent(new CustomEvent('geolocation-ready', {
                                                        detail: data
                                                    }));
                                                }
                                            } catch (e) {
                                                // ignore errors
                                            }
                                        });
                                    }
                                    if (document.readyState === 'loading') {
                                        document.addEventListener('DOMContentLoaded', init);
                                    } else {
                                        init();
                                    }
                                })();
                            </script>
                        </div>

                        <!-- State -->
                        <div>
                            <label for="state_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                State
                            </label>
                            <select wire:model="state_id" id="state_id"
                                wire:change="updateCities($event.target.value)"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('state_id') border-red-500 @enderror">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                City
                            </label>
                            <select wire:model="city_id" id="city_id"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('city_id') border-red-500 @enderror"
                                {{ !$state_id ? 'disabled' : '' }}>
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @if (!$state_id)
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Please select a state first
                                </p>
                            @endif
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="notes"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Notes
                            </label>
                            <textarea wire:model="notes" id="notes" rows="3"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                placeholder="Enter any special notes"></textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('guests.list') }}" wire:navigate
                            class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700 font-medium rounded-lg transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            {{ $isEdit ? 'Update Guest' : 'Add Guest' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
