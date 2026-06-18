@props([
    'name' => 'address',
    'label' => 'Address',
    'placeholder' => 'Start typing to search for an address...',
    'value' => '',
    'wireModel' => null,
    'required' => false,
    'country' => 'ng',
    'restrictToCountry' => true,
    'apiKey' => config('services.google.places_api_key'),
])

@php
    $inputId = 'google-places-' . $name;
    $componentId = 'places-' . uniqid();
    $safeComponentId = preg_replace('/[^a-zA-Z0-9]/', '_', $componentId);
@endphp

{{-- wire:ignore keeps the entire subtree out of Livewire's DOM morph --}}
<div class="w-full relative" wire:ignore>
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input type="text" id="{{ $inputId }}"
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
    {{--
    Script is intentionally NOT inside @push('scripts') because @push is
    silently ignored inside anonymous Blade components. The IIFE guards
    against double-initialisation via data-google-initialized on the input.
--}}
    <script>
        (function() {
            const CONFIG = {
                inputId: '{{ $inputId }}',
                apiKey: '{{ $apiKey }}',
                country: '{{ $country }}',
                restrictToCountry: {{ $restrictToCountry ? 'true' : 'false' }},
                name: '{{ $name }}'
            };

            const state = {
                sessionToken: null,
                debounceTimer: null
            };

            function generateSessionToken() {
                return 'session-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            }

            function getInput() {
                return document.getElementById(CONFIG.inputId);
            }

            function getSuggestionsEl() {
                return document.getElementById(CONFIG.inputId + '-suggestions');
            }

            /* ── Init ─────────────────────────────────────────────────────────── */
            function init() {
                const input = getInput();
                if (!input) return;

                // Guard: only attach listeners once per DOM node lifetime.
                if (input.dataset.googleInitialized === 'true') return;

                state.sessionToken = generateSessionToken();

                input.addEventListener('input', onInput);
                input.addEventListener('keydown', onKeydown);
                document.addEventListener('click', onDocClick);

                input.dataset.googleInitialized = 'true';
                console.log('[GooglePlaces] Initialized for:', CONFIG.inputId);
            }

            /* ── Event handlers ───────────────────────────────────────────────── */
            function onInput(e) {
                clearTimeout(state.debounceTimer);
                const query = e.target.value.trim();
                if (query.length < 3) {
                    hideSuggestions();
                    return;
                }
                state.debounceTimer = setTimeout(() => fetchSuggestions(query), 300);
            }

            function onKeydown(e) {
                const sug = getSuggestionsEl();
                if (!sug) return;
                const items = sug.querySelectorAll('.gp-item');
                const active = sug.querySelector('.gp-item.bg-blue-100');

                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (active) active.click();
                    return;
                }
                if (e.key === 'Escape') {
                    hideSuggestions();
                    return;
                }

                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    let idx = Array.from(items).indexOf(active);
                    idx = e.key === 'ArrowDown' ?
                        (idx < items.length - 1 ? idx + 1 : 0) :
                        (idx > 0 ? idx - 1 : items.length - 1);
                    items.forEach(i => i.classList.remove('bg-blue-100', 'dark:bg-blue-900'));
                    items[idx]?.classList.add('bg-blue-100', 'dark:bg-blue-900');
                }
            }

            function onDocClick(e) {
                const input = getInput();
                const sug = getSuggestionsEl();
                if (input && sug && !input.contains(e.target) && !sug.contains(e.target)) {
                    hideSuggestions();
                }
            }

            /* ── Fetch suggestions ────────────────────────────────────────────── */
            async function fetchSuggestions(query) {
                const body = {
                    input: query,
                    sessionToken: state.sessionToken
                };
                if (CONFIG.restrictToCountry && CONFIG.country) {
                    body.includedRegionCodes = [CONFIG.country];
                }

                try {
                    const res = await fetch('https://places.googleapis.com/v1/places:autocomplete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Goog-Api-Key': CONFIG.apiKey
                        },
                        body: JSON.stringify(body)
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();
                    data.suggestions?.length ? displaySuggestions(data.suggestions) : hideSuggestions();
                } catch (err) {
                    console.error('[GooglePlaces] autocomplete error:', err);
                    hideSuggestions();
                }
            }

            /* ── Render suggestions ───────────────────────────────────────────── */
            function displaySuggestions(suggestions) {
                const sug = getSuggestionsEl();
                if (!sug) return;
                sug.innerHTML = '';

                suggestions.forEach(({
                    placePrediction: p
                }) => {
                    if (!p) return;
                    const div = document.createElement('div');
                    div.className =
                        'gp-item px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 border-b border-gray-100 dark:border-zinc-700 last:border-b-0';
                    div.dataset.placeId = p.placeId;

                    const secondary = p.structuredFormat?.secondaryText?.text ?? '';
                    div.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">${escHtml(p.text.text)}</div>
                        ${secondary ? `<div class="text-xs text-gray-500 dark:text-gray-400">${escHtml(secondary)}</div>` : ''}
                    </div>
                </div>`;

                    div.addEventListener('click', () => selectPlace(p.placeId, p.text.text));
                    div.addEventListener('mouseenter', () => {
                        sug.querySelectorAll('.gp-item').forEach(i => i.classList.remove('bg-blue-100',
                            'dark:bg-blue-900'));
                        div.classList.add('bg-blue-100', 'dark:bg-blue-900');
                    });

                    sug.appendChild(div);
                });

                sug.classList.remove('hidden');
            }

            function hideSuggestions() {
                const sug = getSuggestionsEl();
                if (sug) {
                    sug.classList.add('hidden');
                    sug.innerHTML = '';
                }
            }

            /* ── Select a place ───────────────────────────────────────────────── */
            async function selectPlace(placeId, displayText) {
                const input = getInput();
                hideSuggestions();

                // Show the raw text immediately so the user sees feedback.
                if (input) input.value = displayText;

                try {
                    const res = await fetch(
                        `https://places.googleapis.com/v1/places/${placeId}?sessionToken=${state.sessionToken}`, {
                            headers: {
                                'X-Goog-Api-Key': CONFIG.apiKey,
                                'X-Goog-FieldMask': 'id,displayName,formattedAddress,addressComponents,location'
                            }
                        }
                    );
                    if (!res.ok) throw new Error('HTTP ' + res.status);

                    const place = await res.json();
                    const formattedAddress = place.formattedAddress || displayText;
                    const addressData = parseComponents(place.addressComponents || []);

                    // Update input with the clean formatted address.
                    if (input) input.value = formattedAddress;

                    // ── Tell Livewire about the new address without triggering a re-render
                    //    (@this.dispatch sends a Livewire event; the component's #[On] listener
                    //     handles it server-side without morphing the DOM).
                    @this.dispatch('address-updated', {
                        field: 'address',
                        value: formattedAddress
                    });
                    @this.dispatch('google-places-selected', {
                        place: place,
                        addressData: {
                            formatted_address: formattedAddress,
                            ...addressData
                        },
                        statePath: CONFIG.name
                    });

                    if (addressData.state) {
                        @this.dispatch('google-places-state-detected', {
                            field: 'state_id',
                            stateName: addressData.state
                        });
                    }

                    if (place.location?.lat !== undefined) {
                        @this.dispatch('address-geocoded', {
                            field: 'address',
                            value: formattedAddress,
                            latitude: place.location.lat,
                            longitude: place.location.lng
                        });
                    }

                    // Rotate token after a completed billing session.
                    state.sessionToken = generateSessionToken();
                    console.log('[GooglePlaces] Selected:', formattedAddress);

                } catch (err) {
                    console.error('[GooglePlaces] place details error:', err);
                    // Fallback: still sync the typed text to Livewire.
                    @this.dispatch('address-updated', {
                        field: 'address',
                        value: displayText
                    });
                }
            }

            /* ── Helpers ──────────────────────────────────────────────────────── */
            function parseComponents(components) {
                const map = {};
                components.forEach(c => {
                    map[c.types[0]] = c.longText || '';
                });
                return {
                    street: [map.street_number, map.route].filter(Boolean).join(' '),
                    city: map.locality || map.administrative_area_level_2 || '',
                    state: map.administrative_area_level_1 || '',
                    country: map.country || '',
                    postalCode: map.postal_code || ''
                };
            }

            function escHtml(t) {
                const d = document.createElement('div');
                d.textContent = t;
                return d.innerHTML;
            }

            /* ── Bootstrap ────────────────────────────────────────────────────── */
            // The script is inline so it runs as soon as the component renders.
            // We defer slightly to ensure the input element is in the DOM.
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => setTimeout(init, 50));
            } else {
                setTimeout(init, 50);
            }

            // Also re-run on Livewire SPA navigations.
            document.addEventListener('livewire:navigated', () => setTimeout(init, 50));
        })();
    </script>
@endif
