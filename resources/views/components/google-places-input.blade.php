@props([
    'name' => 'address',
    'label' => 'Address',
    'placeholder' => 'Start typing to search for an address...',
    'value' => '',
    'wireModel' => null,
    'required' => false,
    'country' => 'ng',
    'restrictToCountry' => true,
    // 'apiKey' => config('services.google.places_api_key'),
])

@php
    $inputId = 'google-places-' . $name;
    $componentId = 'places-' . uniqid();
    $safeComponentId = preg_replace('/[^a-zA-Z0-9]/', '_', $componentId);
    $apiKey = config('services.google.places_api_key') ?? (env('GOOGLE_PLACES_API_KEY') ?? '');
@endphp

<div class="w-full relative" wire:ignore>
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input type="text" id="{{ $inputId }}" data-google-places-input="{{ $componentId }}"
        class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error($name) border-red-500 @enderror"
        placeholder="{{ $placeholder }}" autocomplete="off" value="{{ $value ?? '' }}"
        @if ($required) required @endif />

    {{-- Suggestions dropdown --}}
    <div id="{{ $inputId }}-suggestions"
        class="absolute z-50 w-full bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-600 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
    </div>

    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
        Start typing to search for an address. Select from the dropdown suggestions.
    </p>

    @if (!$apiKey)
        <p class="mt-1 text-sm text-red-500">
            Warning: Google Places API key is not configured.
        </p>
    @endif
</div>

@if ($apiKey)
    {{-- @push('scripts') --}}
    <script>
        (function() {
            const CONFIG = {
                inputId: '{{ $inputId }}',
                componentId: '{{ $componentId }}',
                safeComponentId: '{{ $safeComponentId }}',
                apiKey: '{{ $apiKey }}',
                country: '{{ $country }}',
                restrictToCountry: {{ $restrictToCountry ? 'true' : 'false' }},
                name: '{{ $name }}'
            };

            const state = {
                sessionToken: null,
                debounceTimer: null,
                initialized: false
            };

            function generateSessionToken() {
                return 'session-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            }

            function getInput() {
                return document.getElementById(CONFIG.inputId);
            }

            function getSuggestionsContainer() {
                return document.getElementById(CONFIG.inputId + '-suggestions');
            }

            function initGooglePlaces() {
                const input = getInput();
                const suggestionsContainer = getSuggestionsContainer();

                if (!input || !suggestionsContainer) return;

                // Always re-attach; the whole wrapper is wire:ignore so the
                // DOM nodes persist across Livewire re-renders. We use a named
                // handler approach with removeEventListener + addEventListener
                // to avoid duplicate listener stacking if called more than once.
                if (input.dataset.googleInitialized === 'true') return;

                // Generate session token
                state.sessionToken = generateSessionToken();

                // Input event listener with debouncing
                input.addEventListener('input', function(e) {
                    clearTimeout(state.debounceTimer);
                    const query = e.target.value.trim();

                    if (query.length < 3) {
                        hideSuggestions();
                        return;
                    }

                    state.debounceTimer = setTimeout(() => {
                        fetchPlaceSuggestions(query);
                    }, 300);
                });

                // Handle keyboard navigation
                input.addEventListener('keydown', function(e) {
                    const suggestionsEl = getSuggestionsContainer();
                    if (!suggestionsEl) return;
                    const items = suggestionsEl.querySelectorAll('.place-suggestion-item');
                    const activeItem = suggestionsEl.querySelector('.place-suggestion-item.bg-blue-100');

                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (activeItem) activeItem.click();
                        return;
                    }

                    if (e.key === 'Escape') {
                        hideSuggestions();
                        return;
                    }

                    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        let index = Array.from(items).indexOf(activeItem);
                        index = e.key === 'ArrowDown' ?
                            (index < items.length - 1 ? index + 1 : 0) :
                            (index > 0 ? index - 1 : items.length - 1);

                        items.forEach(item => item.classList.remove('bg-blue-100', 'dark:bg-blue-900'));
                        if (items[index]) {
                            items[index].classList.add('bg-blue-100', 'dark:bg-blue-900');
                        }
                    }
                });

                // Hide suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    const inp = getInput();
                    const sug = getSuggestionsContainer();
                    if (inp && sug && !inp.contains(e.target) && !sug.contains(e.target)) {
                        hideSuggestions();
                    }
                });

                input.dataset.googleInitialized = 'true';
                state.initialized = true;
                console.log('[GooglePlaces] Initialized for:', CONFIG.inputId);
            }

            async function fetchPlaceSuggestions(query) {
                try {
                    const requestBody = {
                        input: query,
                        sessionToken: state.sessionToken
                    };

                    if (CONFIG.restrictToCountry && CONFIG.country) {
                        requestBody.includedRegionCodes = [CONFIG.country];
                    }

                    const response = await fetch(`https://places.googleapis.com/v1/places:autocomplete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Goog-Api-Key': CONFIG.apiKey
                        },
                        body: JSON.stringify(requestBody)
                    });

                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const data = await response.json();

                    if (data.suggestions && data.suggestions.length > 0) {
                        displaySuggestions(data.suggestions);
                    } else {
                        hideSuggestions();
                    }
                } catch (error) {
                    console.error('[GooglePlaces] Error fetching suggestions:', error);
                    hideSuggestions();
                }
            }

            function displaySuggestions(suggestions) {
                const suggestionsContainer = getSuggestionsContainer();
                if (!suggestionsContainer) return;

                suggestionsContainer.innerHTML = '';

                suggestions.forEach((suggestion) => {
                    const placePrediction = suggestion.placePrediction;
                    if (!placePrediction) return;

                    const div = document.createElement('div');
                    div.className =
                        'place-suggestion-item px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 border-b border-gray-100 dark:border-zinc-700 last:border-b-0';
                    div.setAttribute('data-place-id', placePrediction.placeId);

                    let html = `<div class="flex items-start">`;
                    html += `<div class="flex-shrink-0 mt-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>`;
                    html += `<div class="ml-3 flex-1">`;
                    html +=
                        `<div class="text-sm font-medium text-gray-900 dark:text-white">${escapeHtml(placePrediction.text.text)}</div>`;

                    if (placePrediction.structuredFormat) {
                        const secondaryText = placePrediction.structuredFormat.secondaryText?.text;
                        if (secondaryText) {
                            html +=
                                `<div class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(secondaryText)}</div>`;
                        }
                    }

                    html += `</div></div>`;
                    div.innerHTML = html;

                    div.addEventListener('click', function() {
                        selectPlace(placePrediction.placeId, placePrediction.text.text);
                    });

                    div.addEventListener('mouseenter', function() {
                        getSuggestionsContainer()
                            ?.querySelectorAll('.place-suggestion-item')
                            .forEach(item => item.classList.remove('bg-blue-100', 'dark:bg-blue-900'));
                        this.classList.add('bg-blue-100', 'dark:bg-blue-900');
                    });

                    suggestionsContainer.appendChild(div);
                });

                suggestionsContainer.classList.remove('hidden');
            }

            function hideSuggestions() {
                const suggestionsContainer = getSuggestionsContainer();
                if (suggestionsContainer) {
                    suggestionsContainer.classList.add('hidden');
                    suggestionsContainer.innerHTML = '';
                }
            }

            async function selectPlace(placeId, displayText) {
                const input = getInput();

                try {
                    const response = await fetch(
                        `https://places.googleapis.com/v1/places/${placeId}?sessionToken=${state.sessionToken}`, {
                            method: 'GET',
                            headers: {
                                'X-Goog-Api-Key': CONFIG.apiKey,
                                'X-Goog-FieldMask': 'id,displayName,formattedAddress,addressComponents,location'
                            }
                        });

                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const place = await response.json();
                    const formattedAddress = place.formattedAddress || displayText;

                    // Update input value directly — no @this.set() to avoid triggering
                    // a Livewire re-render that would destroy and recreate the input node.
                    if (input) input.value = formattedAddress;

                    const addressData = parseAddressComponents(place.addressComponents || []);

                    // ── Notify Livewire via dispatch (not @this.set) so the component
                    //    backend updates its $address property without morphing the DOM.
                    Livewire.dispatch('address-updated', {
                        field: 'address',
                        value: formattedAddress
                    });

                    // ── Also dispatch the full place payload for geocoding / state detection.
                    @this.dispatch('google-places-selected', {
                        place: place,
                        addressData: {
                            formatted_address: formattedAddress,
                            ...addressData
                        },
                        statePath: CONFIG.name
                    });

                    // ── Detect & broadcast state name for server-side state dropdown wiring.
                    if (addressData.state) {
                        @this.dispatch('google-places-state-detected', {
                            field: 'state_id',
                            stateName: addressData.state
                        });
                    }

                    // ── Broadcast lat/lng if returned by the Places Details call.
                    if (place.location?.lat !== undefined && place.location?.lng !== undefined) {
                        @this.dispatch('address-geocoded', {
                            field: 'address',
                            value: formattedAddress,
                            latitude: place.location.lat,
                            longitude: place.location.lng
                        });
                    }

                    // Rotate session token after a completed selection (best practice).
                    state.sessionToken = generateSessionToken();
                    hideSuggestions();

                    console.log('[GooglePlaces] Address selected:', formattedAddress);

                } catch (error) {
                    console.error('[GooglePlaces] Error fetching place details:', error);
                    if (input) input.value = displayText;

                    // Still notify Livewire of the raw text so $address is not stale.
                    Livewire.dispatch('address-updated', {
                        field: 'address',
                        value: displayText
                    });

                    hideSuggestions();
                }
            }

            function parseAddressComponents(components) {
                const result = {
                    street_number: '',
                    route: '',
                    locality: '',
                    administrative_area_level_1: '',
                    administrative_area_level_2: '',
                    country: '',
                    postal_code: ''
                };

                for (const component of components) {
                    const type = component.types[0];
                    const longName = component.longText || '';
                    if (Object.prototype.hasOwnProperty.call(result, type)) {
                        result[type] = longName;
                    }
                }

                return {
                    street: [result.street_number, result.route].filter(Boolean).join(' '),
                    city: result.locality || result.administrative_area_level_2 || '',
                    state: result.administrative_area_level_1 || '',
                    country: result.country || '',
                    postalCode: result.postal_code || ''
                };
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // ── Bootstrap ──────────────────────────────────────────────────────────
            // Run once on livewire:init (fresh page load / SPA navigation).
            document.addEventListener('livewire:init', () => {
                setTimeout(initGooglePlaces, 100);
            });

            // Run immediately if DOM is already ready (e.g. script evaluated late).
            if (document.readyState !== 'loading') {
                setTimeout(initGooglePlaces, 100);
            }
        })();
    </script>
    {{-- @endpush --}}
@endif
