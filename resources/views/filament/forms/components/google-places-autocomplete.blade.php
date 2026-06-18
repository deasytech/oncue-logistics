@php
    $apiKey = $getApiKey();
    $country = $getCountry();
    $restrictToCountry = $shouldRestrictToCountry();
    $addressFields = $getAddressFields();
    $statePath = $getStatePath();
    $inputId = $getId();
    $safeInputId = preg_replace('/[^a-zA-Z0-9]/', '_', $inputId);
@endphp

<div x-data="googlePlaces{{ $safeInputId }}()" x-init="init()" class="relative">
    <input x-ref="input" type="text" {!! $getExtraInputAttributeBag() !!} wire:model.live="{{ $getStatePath() }}"
        dusk="filament.forms.{{ $getStatePath() }}" id="{{ $getId() }}"
        class="fi-input block w-full rounded-lg border-none bg-white py-1.5 text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6"
        autocomplete="off" placeholder="{{ $getPlaceholder() ?? 'Start typing to search for an address...' }}" />

    {{-- Suggestions dropdown --}}
    <div x-ref="suggestions" id="{{ $inputId }}-suggestions"
        class="absolute z-50 w-full bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-600 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
    </div>
</div>

@if ($apiKey)
    @push('scripts')
        <script>
            function googlePlaces{{ $safeInputId }}() {
                return {
                    apiKey: '{{ $apiKey }}',
                    country: '{{ $country }}',
                    restrictToCountry: {{ $restrictToCountry ? 'true' : 'false' }},
                    statePath: '{{ $statePath }}',
                    addressFields: @json($addressFields),
                    sessionToken: null,
                    debounceTimer: null,

                    init() {
                        this.generateSessionToken();
                        this.setupEventListeners();
                    },

                    generateSessionToken() {
                        this.sessionToken = 'session-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                    },

                    setupEventListeners() {
                        const input = this.$refs.input;
                        const suggestions = this.$refs.suggestions;

                        // Input event listener with debouncing
                        input.addEventListener('input', (e) => {
                            clearTimeout(this.debounceTimer);
                            const query = e.target.value.trim();

                            if (query.length < 3) {
                                this.hideSuggestions();
                                return;
                            }

                            this.debounceTimer = setTimeout(() => {
                                this.fetchPlaceSuggestions(query);
                            }, 300);
                        });

                        // Handle keyboard navigation
                        input.addEventListener('keydown', (e) => {
                            const items = suggestions.querySelectorAll('.place-suggestion-item');
                            const activeItem = suggestions.querySelector('.place-suggestion-item.bg-blue-100');

                            if (e.key === 'Enter') {
                                e.preventDefault();
                                if (activeItem) {
                                    activeItem.click();
                                }
                                return;
                            }

                            if (e.key === 'Escape') {
                                this.hideSuggestions();
                                return;
                            }

                            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                                e.preventDefault();
                                let index = Array.from(items).indexOf(activeItem);

                                if (e.key === 'ArrowDown') {
                                    index = index < items.length - 1 ? index + 1 : 0;
                                } else {
                                    index = index > 0 ? index - 1 : items.length - 1;
                                }

                                items.forEach(item => item.classList.remove('bg-blue-100', 'dark:bg-blue-900'));
                                if (items[index]) {
                                    items[index].classList.add('bg-blue-100', 'dark:bg-blue-900');
                                }
                            }
                        });

                        // Hide suggestions when clicking outside
                        document.addEventListener('click', (e) => {
                            if (!input.contains(e.target) && !suggestions.contains(e.target)) {
                                this.hideSuggestions();
                            }
                        });
                    },

                    async fetchPlaceSuggestions(query) {
                        const suggestions = this.$refs.suggestions;

                        try {
                            const requestBody = {
                                input: query,
                                sessionToken: this.sessionToken
                            };

                            if (this.restrictToCountry && this.country) {
                                requestBody.includedRegionCodes = [this.country];
                            }

                            const response = await fetch(`https://places.googleapis.com/v1/places:autocomplete`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Goog-Api-Key': this.apiKey
                                },
                                body: JSON.stringify(requestBody)
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.suggestions && data.suggestions.length > 0) {
                                this.displaySuggestions(data.suggestions);
                            } else {
                                this.hideSuggestions();
                            }
                        } catch (error) {
                            console.error('[GooglePlaces] Error fetching suggestions:', error);
                            this.hideSuggestions();
                        }
                    },

                    displaySuggestions(suggestions) {
                        const container = this.$refs.suggestions;
                        container.innerHTML = '';

                        suggestions.forEach((suggestion) => {
                            const placePrediction = suggestion.placePrediction;
                            if (!placePrediction) return;

                            const div = document.createElement('div');
                            div.className =
                                'place-suggestion-item px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 border-b border-gray-100 dark:border-zinc-700 last:border-b-0';
                            div.setAttribute('data-place-id', placePrediction.placeId);

                            // Build the suggestion HTML
                            let html = `<div class="flex items-start">`;
                            html += `<div class="flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>`;

                            html += `<div class="ml-3 flex-1">`;
                            html +=
                                `<div class="text-sm font-medium text-gray-900 dark:text-white">${this.escapeHtml(placePrediction.text.text)}</div>`;

                            if (placePrediction.structuredFormat) {
                                const secondaryText = placePrediction.structuredFormat.secondaryText?.text;
                                if (secondaryText) {
                                    html +=
                                        `<div class="text-xs text-gray-500 dark:text-gray-400">${this.escapeHtml(secondaryText)}</div>`;
                                }
                            }

                            html += `</div></div>`;
                            div.innerHTML = html;

                            div.addEventListener('click', () => {
                                this.selectPlace(placePrediction.placeId, placePrediction.text.text);
                            });

                            // Add hover effect for keyboard navigation
                            div.addEventListener('mouseenter', function() {
                                container.querySelectorAll('.place-suggestion-item').forEach(item => {
                                    item.classList.remove('bg-blue-100', 'dark:bg-blue-900');
                                });
                                this.classList.add('bg-blue-100', 'dark:bg-blue-900');
                            });

                            container.appendChild(div);
                        });

                        container.classList.remove('hidden');
                    },

                    hideSuggestions() {
                        const suggestions = this.$refs.suggestions;
                        if (suggestions) {
                            suggestions.classList.add('hidden');
                            suggestions.innerHTML = '';
                        }
                    },

                    async selectPlace(placeId, displayText) {
                        const input = this.$refs.input;

                        try {
                            // Fetch place details
                            const response = await fetch(
                                `https://places.googleapis.com/v1/places/${placeId}?sessionToken=${this.sessionToken}`, {
                                    method: 'GET',
                                    headers: {
                                        'X-Goog-Api-Key': this.apiKey,
                                        'X-Goog-FieldMask': 'id,displayName,formattedAddress,addressComponents,location'
                                    }
                                });

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const place = await response.json();

                            // Get formatted address
                            const formattedAddress = place.formattedAddress || displayText;

                            // Update input and Livewire model
                            input.value = formattedAddress;
                            this.$wire.set(this.statePath, formattedAddress);

                            // Parse address components
                            const addressData = this.parseAddressComponents(place.addressComponents || []);

                            // Update related fields if configured
                            if (this.addressFields) {
                                if (this.addressFields.state && addressData.state) {
                                    this.findAndSetState(addressData.state);
                                }
                                if (this.addressFields.city && addressData.city) {
                                    this.$wire.set(this.addressFields.city, addressData.city);
                                }
                            }

                            // Dispatch event
                            this.$dispatch('google-places-selected', {
                                place: place,
                                addressData: addressData,
                                statePath: this.statePath
                            });

                            // Generate new session token
                            this.generateSessionToken();
                            this.hideSuggestions();

                            console.log('[GooglePlaces] Address selected:', formattedAddress);

                        } catch (error) {
                            console.error('[GooglePlaces] Error fetching place details:', error);
                            input.value = displayText;
                            this.$wire.set(this.statePath, displayText);
                            this.hideSuggestions();
                        }
                    },

                    parseAddressComponents(components) {
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
                            const shortName = component.shortText || '';

                            if (result.hasOwnProperty(type)) {
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
                    },

                    findAndSetState(stateName) {
                        this.$dispatch('google-places-state-selected', {
                            stateName: stateName,
                            statePath: this.addressFields.state
                        });
                    },

                    escapeHtml(text) {
                        const div = document.createElement('div');
                        div.textContent = text;
                        return div.innerHTML;
                    }
                }
            }
        </script>
    @endpush
@else
    <div class="text-red-500 text-sm mt-1">
        Warning: Google Places API key is not configured.
    </div>
@endif
