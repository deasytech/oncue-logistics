<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>RSVP Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css') <!-- Tailwind CSS should be compiled here -->
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800">Confirm Your Attendance</h2>

        {{-- Event Logo and Description --}}
        @if ($event->logo || $event->description)
            <div class="mt-6 text-center">
                @if ($event->logo)
                    <div class="flex justify-center mb-4">
                        <img src="{{ Storage::url($event->logo) }}" alt="{{ $event->name }} Logo"
                            class="h-40 w-auto object-contain rounded-lg">
                    </div>
                @endif
                @if ($event->description)
                    <p class="text-gray-600 text-sm max-w-md mx-auto">{{ $event->description }}</p>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="mt-4 bg-red-100 text-red-700 text-center py-2 px-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success') || in_array($rsvp_data->attendance_status, ['confirmed', 'declined']))
            <!-- Submission Summary Page -->
            <div class="mt-8 text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-800 mb-2">Thank You!</h3>
                <p class="text-gray-600 mb-6">Your response has been recorded successfully.</p>

                @if ($rsvp_data->attendance_status === 'confirmed')
                    <!-- Notification Message -->
                    <div class="bg-pink-50 border border-pink-200 rounded-lg p-6 mb-6 text-left">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-pink-500 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-pink-800 mb-1">Delivery Notifications</h4>
                                <p class="text-pink-700 text-sm">
                                    Please note a delivery schedule will be shared 48 hours to your delivery date.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Response Summary -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                    <h4 class="font-semibold text-gray-800 mb-4 pb-2 border-b">Your Response Summary</h4>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Attendance:</span>
                            <span
                                class="font-medium {{ $rsvp_data->attendance_status === 'confirmed' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $rsvp_data->attendance_status === 'confirmed' ? 'Yes, I will attend' : 'No, I can\'t make it' }}
                            </span>
                        </div>

                        {{-- @if ($rsvp_data->plus_one && $rsvp_data->plus_one !== '0' && $rsvp_data->attendance_status === 'confirmed')
                            <div class="flex justify-between">
                                <span class="text-gray-600">Plus One:</span>
                                <span class="font-medium text-gray-800">{{ $rsvp_data->plus_one }}</span>
                            </div>
                        @endif --}}

                        @if (
                            $existingFabricSelection &&
                                $existingFabricSelection->fabricSelections &&
                                $existingFabricSelection->fabricSelections->count() > 0)
                            <div class="pt-2 border-t">
                                <span class="text-gray-600 block mb-2">Selected Fabrics:</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($existingFabricSelection->fabricSelections as $fabric)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                            {{ $fabric->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex justify-between pt-2">
                                <span class="text-gray-600">Payment Method:</span>
                                <span
                                    class="font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $existingFabricSelection->payment_method) }}</span>
                            </div>
                        @endif
                        @if ($rsvp_data->attendance_status === 'confirmed')
                            <div class="pt-2 border-t">
                                <span class="text-gray-600 block mb-1">Delivery Address:</span>
                                <span class="font-medium text-gray-800 text-sm">{{ $deliveryAddress['address'] }}</span>
                                <p class="text-gray-600 text-sm">{{ $deliveryAddress['city'] }},
                                    {{ $deliveryAddress['state'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="text-sm text-gray-500">
                    <p>RSVP expires: {{ \Carbon\Carbon::parse($rsvp_data->rsvp_expires_at)->format('M d, Y h:i A') }}
                    </p>
                </div>

                <button disabled
                    class="w-full bg-green-600 text-white font-semibold py-3 px-4 rounded-md mt-6 cursor-default">
                    Response Submitted ✓
                </button>
            </div>
        @else
            <form method="POST" action="{{ route('rsvp.submit', $token) }}" class="mt-6 space-y-4">
                @csrf

                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Event: {{ $event->name }}</h3>
                </div>

                <div>
                    <label for="attendance_status" class="block font-semibold text-gray-700 mb-1">Will you
                        attend?</label>
                    <select name="attendance_status" id="attendance_status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500">
                        <option value="">-- Select Option --</option>
                        <option value="confirmed"
                            {{ $rsvp_data->attendance_status === 'confirmed' ? 'selected' : '' }}>Yes,
                            I
                            will attend</option>
                        <option value="declined" {{ $rsvp_data->attendance_status === 'declined' ? 'selected' : '' }}>
                            No, I
                            can't make it</option>
                    </select>
                </div>

                <!-- Fabric Purchase Prompt for Declined Guests -->
                <div id="fabric-purchase-prompt" class="hidden border-t pt-6 mt-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Fabric Purchase</h4>
                    <p class="text-gray-600 mb-4">We understand you can't attend, but would you like to purchase fabric
                        from this event?</p>

                    <div class="flex space-x-4 mb-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="fabric_purchase_interest" value="yes"
                                id="fabric-purchase-yes" class="text-pink-600 focus:ring-pink-500">
                            <span class="font-medium text-gray-700">Yes, I'd like to purchase fabric</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="fabric_purchase_interest" value="no" id="fabric-purchase-no"
                                class="text-pink-600 focus:ring-pink-500">
                            <span class="font-medium text-gray-700">No, thank you</span>
                        </label>
                    </div>
                </div>

                {{-- <div id="plus-one-section">
                    <label for="plus_one" class="block font-semibold text-gray-700 mb-1">Plus one (guest name)?</label>
                    <input type="text" name="plus_one" id="plus_one"
                        value="{{ $rsvp_data->plus_one && $rsvp_data->plus_one !== '0' ? $rsvp_data->plus_one : '' }}"
                        placeholder="Enter name of your guest (optional)" pattern="[A-Za-z\s'\-]+"
                        title="Please enter letters only. Spaces, apostrophes, and hyphens are allowed."
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500" />
                </div> --}}

                <!-- Fabric Selection Section -->
                @if ($fabricTypes->count() > 0)
                    <div class="border-t pt-6 mt-6" data-section="fabric-selection">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Fabric Selections</h4>
                        <div class="space-y-3">
                            @foreach ($fabricTypes as $fabric)
                                @php
                                    $isInvitationCard = stripos($fabric->name, 'invitation card') !== false;
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition">
                                    <label class="flex items-start space-x-3 cursor-pointer">
                                        <input type="checkbox" name="fabric_types[]" value="{{ $fabric->id }}"
                                            class="mt-1 text-pink-600 focus:ring-pink-500"
                                            {{ $isInvitationCard ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h5 class="font-semibold text-gray-800">{{ $fabric->name }}</h5>
                                                    @if ($fabric->description)
                                                        <p class="text-sm text-gray-600">{{ $fabric->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-semibold text-pink-600">
                                                        ₦{{ number_format($fabric->base_price, 2) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Select the fabrics you're interested in. You can choose
                            multiple fabrics.</p>
                    </div>
                @endif

                <!-- Payment Method Section -->
                @if ($fabricTypes->count() > 0)
                    <div class="border-t pt-6 mt-6" data-section="payment-method">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Payment Method</h4>
                        <input type="hidden" name="payment_method" value="online">
                        <div class="space-y-3">
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="payment_method" value="online" checked
                                        class="text-pink-600 focus:ring-pink-500" disabled>
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-800">Online Payment (Card, transfer, Bank
                                            and USSD)</h5>
                                        <p class="text-sm text-gray-600">Pay securely online with your card</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Delivery Address Section -->
                <div id="delivery-address-section" class="border-t pt-6 mt-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Delivery Address</h4>
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <p class="text-sm text-gray-600 mb-2">Current address:</p>
                        <p class="font-medium text-gray-800">{{ $deliveryAddress['address'] }}</p>
                        <p class="text-gray-600">{{ $deliveryAddress['city'] }}, {{ $deliveryAddress['state'] }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="update_address" name="update_address" value="1"
                                class="text-pink-600 focus:ring-pink-500">
                            <span class="text-sm font-medium text-gray-700">I want to update my delivery address</span>
                        </label>
                    </div>

                    <div id="address_update_fields" class="hidden space-y-4">
                        <!-- Google Places Address Autocomplete -->
                        <div>
                            <label for="delivery_address" class="block font-semibold text-gray-700 mb-1">Street
                                Address</label>
                            <input type="text" name="delivery_address" id="delivery_address"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Start typing to search for an address..."
                                value="{{ $deliveryAddress['address'] }}" autocomplete="off">
                            <p class="mt-1 text-xs text-gray-500">Start typing to search for an address. Select from
                                the dropdown suggestions.</p>

                            <!-- Hidden fields to capture geocoded latitude/longitude -->
                            <input type="hidden" name="delivery_latitude" id="delivery_latitude"
                                value="{{ $guest->latitude ?? '' }}">
                            <input type="hidden" name="delivery_longitude" id="delivery_longitude"
                                value="{{ $guest->longitude ?? '' }}">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="delivery_state_id"
                                    class="block font-semibold text-gray-700 mb-1">State</label>
                                <select name="delivery_state_id" id="delivery_state_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Select state</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ (string) $selectedStateId === (string) $state->id ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="delivery_city_id"
                                    class="block font-semibold text-gray-700 mb-1">City</label>
                                <select name="delivery_city_id" id="delivery_city_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500"
                                    {{ $selectedStateId ? '' : 'disabled' }}>
                                    <option value="">Select city</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ (string) $selectedCityId === (string) $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Zone Selection -->
                    <div class="mt-4">
                        <label class="block font-semibold text-gray-700 mb-3">
                            Delivery Method
                        </label>

                        <div id="shipping-category-container" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        </div>

                        <div id="shipping-zone-container" class="mt-4 space-y-3">
                        </div>

                    </div>
                    {{-- <div class="mt-4">
                        <label for="delivery_zone_id" class="block font-semibold text-gray-700 mb-1">Delivery
                            Zone</label>
                        <select name="delivery_zone_id" id="delivery_zone_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500"
                            disabled>
                            <option value="">Loading delivery zones...</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Select your preferred delivery zone.</p>
                    </div> --}}
                </div>

                <!-- Delivery Zone functionality moved to Package Customizer -->
                <!-- Delivery zones are now handled in the package customizer page -->

                <div id="rsvp-expiry-section" class="text-sm text-gray-600 border-t pt-4">
                    <p>RSVP expires: {{ \Carbon\Carbon::parse($rsvp_data->rsvp_expires_at)->format('M d, Y h:i A') }}
                    </p>
                </div>

                <button type="submit" id="submit-button"
                    class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-4 rounded-md transition disabled:opacity-50 disabled:cursor-not-allowed"
                    @if (session('success')) disabled @endif>
                    @if (session('success'))
                        Response Submitted ✓
                    @else
                        Continue
                    @endif
                </button>
            </form>
        @endif
    </div>

    <script>
        // Show/hide address update fields based on checkbox
        document.getElementById('update_address').addEventListener('change', function() {
            const addressFields = document.getElementById('address_update_fields');
            if (this.checked) {
                addressFields.classList.remove('hidden');
            } else {
                addressFields.classList.add('hidden');
            }
        });

        const stateSelect = document.getElementById('delivery_state_id');
        const citySelect = document.getElementById('delivery_city_id');
        // const shippingZoneSelect = document.getElementById('delivery_zone_id');
        const selectedCityId = '{{ $selectedCityId }}';

        // Shipping Zone Configuration
        const deliveryConfig = {
            middlewareUrl: '{{ config('services.delivery.middleware_url', '') }}',
            zonesEndpoint: '/api/v1/common/zones'
        };

        const resetCities = (placeholder = 'Select city') => {
            citySelect.innerHTML = `<option value="">${placeholder}</option>`;
            citySelect.disabled = true;
        };

        const populateCities = (cities) => {
            resetCities();
            if (!cities.length) {
                resetCities('No cities available');
                return;
            }

            cities.forEach((city) => {
                const option = document.createElement('option');
                option.value = city.id;
                option.textContent = city.name;
                if (selectedCityId && String(city.id) === String(selectedCityId)) {
                    option.selected = true;
                }
                citySelect.appendChild(option);
            });
            citySelect.disabled = false;
        };

        const fetchCities = async (stateId) => {
            if (!stateId) {
                resetCities();
                return;
            }

            resetCities('Loading cities...');

            try {
                const response = await fetch(`/rsvp/states/${stateId}/cities`);
                if (!response.ok) {
                    throw new Error('Failed to load cities');
                }
                const data = await response.json();
                populateCities(data);
            } catch (error) {
                console.error(error);
                resetCities('Unable to load cities');
            }
        };

        // Reset shipping zones
        // const resetShippingZones = (placeholder = 'Select shipping zone') => {
        //     shippingZoneSelect.innerHTML = `<option value="">${placeholder}</option>`;
        //     shippingZoneSelect.disabled = true;
        // };

        const shippingData = {
            categories: [],
            selectedCategory: null
        };

        const populateShippingZones = (categories) => {
            shippingData.categories = categories;

            const categoryContainer = document.getElementById('shipping-category-container');
            const zoneContainer = document.getElementById('shipping-zone-container');

            if (!categories || !categories.length) {
                categoryContainer.innerHTML = `
            <div class="text-sm text-red-500">
                No delivery options available
            </div>
        `;
                return;
            }

            categoryContainer.innerHTML = categories.map(category => `
        <button
            type="button"
            class="shipping-category-btn border rounded-lg p-4 text-left hover:border-pink-500 w-full"
            data-category="${category.category}">
            <div class="font-semibold">${category.label}</div>
            <div class="text-xs text-gray-500">
                ${category.zones.length} available locations
            </div>
        </button>
    `).join('');

            document.querySelectorAll('.shipping-category-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const category = categories.find(
                        c => c.category === btn.dataset.category
                    );

                    shippingData.selectedCategory = category;

                    document.querySelectorAll('.shipping-category-btn')
                        .forEach(b => b.classList.remove(
                            'border-pink-500',
                            'bg-pink-50'
                        ));

                    btn.classList.add(
                        'border-pink-500',
                        'bg-pink-50'
                    );

                    zoneContainer.innerHTML = category.zones.map(zone => `
                <label class="block border rounded-lg p-4 hover:border-pink-500 cursor-pointer">
                    <input
                        type="radio"
                        name="delivery_zone_id"
                        value="${zone.id}"
                        class="mr-3">

                    <span class="font-medium">
                        ${zone.name}
                    </span>

                    <span class="float-right font-semibold text-pink-600">
                        ₦${Number(zone.effective_price).toLocaleString()}
                    </span>

                    <div class="text-sm text-gray-500 mt-1">
                        ${zone.description ?? ''}
                    </div>
                </label>
            `).join('');
                });
            });
        };

        // Fetch shipping zones from delivery middleware
        const fetchShippingZones = async () => {
            // Check if delivery middleware is configured
            if (!deliveryConfig.middlewareUrl) {
                console.warn('Delivery middleware not configured');
                // resetShippingZones('Shipping zones unavailable');
                return;
            }

            // resetShippingZones('Loading shipping zones...');

            try {
                const url = `${deliveryConfig.middlewareUrl}${deliveryConfig.zonesEndpoint}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Failed to load shipping zones: ${response.status}`);
                }

                const result = await response.json();
                console.log(result)
                // Handle response structure: {success: true, count: N, data: [...]}
                const categories = result.data || [];
                populateShippingZones(categories);
            } catch (error) {
                console.error('Error fetching shipping zones:', error);
                resetShippingZones('Unable to load shipping zones');
            }
        };

        stateSelect.addEventListener('change', (event) => {
            fetchCities(event.target.value);
        });

        if (stateSelect.value && citySelect.options.length <= 1) {
            fetchCities(stateSelect.value);
        }

        // Fetch shipping zones on page load
        fetchShippingZones();

        // Handle fabric purchase prompt for declined guests
        const attendanceSelect = document.getElementById('attendance_status');
        const fabricPurchasePrompt = document.getElementById('fabric-purchase-prompt');
        const fabricPurchaseYes = document.getElementById('fabric-purchase-yes');
        const fabricPurchaseNo = document.getElementById('fabric-purchase-no');
        const fabricSelectionSection = document.querySelector(
            '[data-section="fabric-selection"]'); // Fabric Selection Section
        const paymentMethodSection = document.querySelector('[data-section="payment-method"]'); // Payment Method Section
        const plusOneSection = document.getElementById('plus-one-section');
        const deliveryAddressSection = document.getElementById('delivery-address-section');
        const rsvpExpirySection = document.getElementById('rsvp-expiry-section');
        const plusOneInput = document.getElementById('plus_one');
        const updateAddressCheckbox = document.getElementById('update_address');
        const addressUpdateFields = document.getElementById('address_update_fields');

        function toggleAttendanceDependentSections() {
            const shouldShowSections = attendanceSelect.value === 'confirmed' || fabricPurchaseYes.checked;

            if (plusOneSection) {
                plusOneSection.classList.toggle('hidden', !shouldShowSections);
            }

            if (deliveryAddressSection) {
                deliveryAddressSection.classList.toggle('hidden', !shouldShowSections);
            }

            if (rsvpExpirySection) {
                rsvpExpirySection.classList.toggle('hidden', attendanceSelect.value === 'declined');
            }

            if (!shouldShowSections) {
                if (plusOneInput) {
                    plusOneInput.value = '';
                }

                if (updateAddressCheckbox) {
                    updateAddressCheckbox.checked = false;
                }

                if (addressUpdateFields) {
                    addressUpdateFields.classList.add('hidden');
                }
            }
        }

        // Show/hide fabric purchase prompt based on attendance status
        function toggleFabricPurchasePrompt() {
            const isDeclined = attendanceSelect.value === 'declined';

            if (isDeclined) {
                fabricPurchasePrompt.classList.remove('hidden');
                // Reset fabric purchase selection when showing prompt
                if (!fabricPurchaseYes.checked && !fabricPurchaseNo.checked) {
                    fabricPurchaseNo.checked = true; // Default to "No"
                }
                toggleFabricSelectionVisibility();
            } else {
                fabricPurchasePrompt.classList.add('hidden');
                // Show fabric selection for confirmed guests
                if (fabricSelectionSection) {
                    fabricSelectionSection.classList.remove('hidden');
                }
                if (paymentMethodSection) {
                    paymentMethodSection.classList.remove('hidden');
                }
            }

            toggleAttendanceDependentSections();
        }

        // Show/hide fabric selection based on purchase interest
        function toggleFabricSelectionVisibility() {
            const wantsToPurchase = fabricPurchaseYes.checked;

            if (fabricSelectionSection) {
                if (wantsToPurchase) {
                    fabricSelectionSection.classList.remove('hidden');
                } else {
                    fabricSelectionSection.classList.add('hidden');
                    // Clear fabric selections when hiding
                    const checkboxes = fabricSelectionSection.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => checkbox.checked = false);
                }
            }

            if (paymentMethodSection) {
                if (wantsToPurchase) {
                    paymentMethodSection.classList.remove('hidden');
                } else {
                    paymentMethodSection.classList.add('hidden');
                }
            }

            toggleAttendanceDependentSections();
        }

        // Initialize based on current selection
        toggleFabricPurchasePrompt();

        // Add event listeners
        attendanceSelect.addEventListener('change', toggleFabricPurchasePrompt);

        if (fabricPurchaseYes) {
            fabricPurchaseYes.addEventListener('change', toggleFabricSelectionVisibility);
        }

        if (fabricPurchaseNo) {
            fabricPurchaseNo.addEventListener('change', toggleFabricSelectionVisibility);
        }

        // Handle form submission and disable submit button
        try {
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                const submitButton = document.getElementById('submit-button');

                // Check if already submitted (from session success)
                @if (session('success'))
                    // Already disabled via Blade, but add extra protection
                    submitButton.disabled = true;
                    submitButton.textContent = 'Response Submitted ✓';
                    submitButton.classList.add('bg-green-600', 'hover:bg-green-700');
                    submitButton.classList.remove('bg-pink-600', 'hover:bg极速赛车开奖结果查询-pink-700');
                @endif

                // Handle form submission
                form.addEventListener('submit', function(e) {
                    console.log('Form submission started');

                    // Validate required fields
                    const attendanceStatus = document.getElementById('attendance_status').value;
                    console.log('Attendance status:', attendanceStatus);

                    if (!attendanceStatus) {
                        e.preventDefault();
                        alert('Please select your attendance status.');
                        return;
                    }

                    // For declined guests, check if they want to purchase fabric
                    if (attendanceStatus === 'declined') {
                        const wantsToPurchase = document.querySelector(
                            'input[name="fabric_purchase_interest"]:checked');
                        console.log('Fabric purchase interest:', wantsToPurchase?.value);

                        if (!wantsToPurchase) {
                            e.preventDefault();
                            alert('Please indicate whether you would like to purchase fabric.');
                            return;
                        }

                        // If they don't want to purchase fabric, ensure fabric selection is cleared and no validation conflicts
                        if (wantsToPurchase.value === 'no') {
                            const fabricCheckboxes = document.querySelectorAll(
                                'input[name="fabric_types[]"]');
                            fabricCheckboxes.forEach(checkbox => checkbox.checked = false);

                            // Remove required attributes from fabric selection to prevent validation conflicts
                            const fabricSection = document.querySelector(
                                '[data-section="fabric-selection"]');
                            if (fabricSection) {
                                const checkboxes = fabricSection.querySelectorAll('input[type="checkbox"]');
                                checkboxes.forEach(cb => cb.removeAttribute('required'));
                            }

                            // Remove required attributes from payment method section
                            const paymentMethodSection = document.querySelector(
                                '[data-section="payment-method"]');
                            极速赛车开奖结果查询
                            if (paymentMethodSection) {
                                const radioButtons = paymentMethodSection.querySelectorAll(
                                    'input[type="radio"]');
                                radioButtons.forEach(rb => rb.removeAttribute('required'));
                            }
                        }
                    }

                    // If we reach here, validation passed - allow form to submit naturally
                    // Disable submit button and show loading state to prevent double submission
                    submitButton.disabled = true;
                    submitButton.textContent = 'Submitting...';
                    submitButton.classList.add('opacity-75', 'cursor-not-allowed');

                    // Optional: Add visual feedback
                    submitButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Submitting...
                    `;
                });
            });
        } catch (error) {
            console.error('RSVP JavaScript error:', error);
            // Fallback: ensure form can still submit by removing problematic validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Basic validation only
                    const attendanceStatus = document.getElementById('attendance_status').value;
                    if (!attendanceStatus) {
                        e.preventDefault();
                        alert('Please select your attendance status.');
                        return;
                    }
                });
            }
        }
    </script>

    <!-- Google Places API Script (New) -->
    <script>
        (function() {
            const apiKey = '{{ config('services.google.places_api_key', '') }}';
            const countryRestriction = 'ng';

            if (!apiKey || apiKey === 'your_google_places_api_key_here') {
                console.warn('Google Places API key not configured');
                return;
            }

            const state = {
                sessionToken: null,
                debounceTimer: null
            };

            function generateSessionToken() {
                return 'session-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            }

            function initGooglePlaces() {
                const addressInput = document.getElementById('delivery_address');
                if (!addressInput) return;

                // Check if already initialized
                if (addressInput.dataset.placesInitialized === 'true') return;

                // Generate a session token for billing optimization
                state.sessionToken = generateSessionToken();

                // Create suggestions container if it doesn't exist
                let suggestionsContainer = document.getElementById('delivery_address_suggestions');
                if (!suggestionsContainer) {
                    suggestionsContainer = document.createElement('div');
                    suggestionsContainer.id = 'delivery_address_suggestions';
                    suggestionsContainer.className =
                        'absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto';
                    addressInput.parentNode.style.position = 'relative';
                    addressInput.parentNode.appendChild(suggestionsContainer);
                }

                // Input event listener with debouncing
                addressInput.addEventListener('input', function(e) {
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
                addressInput.addEventListener('keydown', function(e) {
                    const items = suggestionsContainer.querySelectorAll('.place-suggestion-item');
                    const activeItem = suggestionsContainer.querySelector('.place-suggestion-item.bg-blue-100');

                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (activeItem) {
                            activeItem.click();
                        }
                        return;
                    }

                    if (e.key === 'Escape') {
                        hideSuggestions();
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

                        items.forEach(item => item.classList.remove('bg-blue-100'));
                        if (items[index]) {
                            items[index].classList.add('bg-blue-100');
                        }
                    }
                });

                // Hide suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (!addressInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                        hideSuggestions();
                    }
                });

                // Mark as initialized
                addressInput.dataset.placesInitialized = 'true';
                console.log('[GooglePlaces] Successfully initialized for delivery_address');
            }

            async function fetchPlaceSuggestions(query) {
                const suggestionsContainer = document.getElementById('delivery_address_suggestions');

                try {
                    const requestBody = {
                        input: query,
                        includedRegionCodes: [countryRestriction],
                        sessionToken: state.sessionToken
                    };

                    const response = await fetch(`https://places.googleapis.com/v1/places:autocomplete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Goog-Api-Key': apiKey
                        },
                        body: JSON.stringify(requestBody)
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

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
                const suggestionsContainer = document.getElementById('delivery_address_suggestions');

                suggestionsContainer.innerHTML = '';

                suggestions.forEach((suggestion, index) => {
                    const placePrediction = suggestion.placePrediction;
                    if (!placePrediction) return;

                    const div = document.createElement('div');
                    div.className =
                        'place-suggestion-item px-4 py-3 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0';
                    div.setAttribute('data-place-id', placePrediction.placeId);
                    div.setAttribute('data-text', placePrediction.text.text);

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
                        `<div class="text-sm font-medium text-gray-900">${escapeHtml(placePrediction.text.text)}</div>`;

                    if (placePrediction.structuredFormat) {
                        const mainText = placePrediction.structuredFormat.mainText.text;
                        const secondaryText = placePrediction.structuredFormat.secondaryText?.text;
                        if (secondaryText && secondaryText !== mainText) {
                            html += `<div class="text-xs text-gray-500">${escapeHtml(secondaryText)}</div>`;
                        }
                    }

                    html += `</div></div>`;
                    div.innerHTML = html;

                    div.addEventListener('click', function() {
                        selectPlace(placePrediction.placeId, placePrediction.text.text);
                    });

                    // Add hover effect for keyboard navigation
                    div.addEventListener('mouseenter', function() {
                        suggestionsContainer.querySelectorAll('.place-suggestion-item').forEach(
                            item => {
                                item.classList.remove('bg-blue-100');
                            });
                        this.classList.add('bg-blue-100');
                    });

                    suggestionsContainer.appendChild(div);
                });

                suggestionsContainer.classList.remove('hidden');
            }

            function hideSuggestions() {
                const suggestionsContainer = document.getElementById('delivery_address_suggestions');
                if (suggestionsContainer) {
                    suggestionsContainer.classList.add('hidden');
                    suggestionsContainer.innerHTML = '';
                }
            }

            async function selectPlace(placeId, displayText) {
                const addressInput = document.getElementById('delivery_address');

                try {
                    // Fetch place details
                    const response = await fetch(
                        `https://places.googleapis.com/v1/places/${placeId}?sessionToken=${state.sessionToken}`, {
                            method: 'GET',
                            headers: {
                                'X-Goog-Api-Key': apiKey,
                                'X-Goog-FieldMask': 'id,displayName,formattedAddress,addressComponents,location'
                            }
                        });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const place = await response.json();

                    // Get formatted address
                    const formattedAddress = place.formattedAddress || displayText;

                    // Update input
                    addressInput.value = formattedAddress;

                    // Extract state and city from address components
                    let stateName = '';
                    let cityName = '';

                    if (place.addressComponents) {
                        for (const component of place.addressComponents) {
                            const type = component.types[0];
                            const longName = component.longText || '';

                            if (type === 'administrative_area_level_1') {
                                stateName = longName;
                            }

                            if ((type === 'locality' || type === 'administrative_area_level_2') && !cityName) {
                                cityName = longName;
                            }
                        }
                    }

                    // Auto-select state if found
                    if (stateName) {
                        const stateSelect = document.getElementById('delivery_state_id');
                        if (stateSelect) {
                            for (let i = 0; i < stateSelect.options.length; i++) {
                                if (stateSelect.options[i].text.toLowerCase() === stateName.toLowerCase() ||
                                    stateName.toLowerCase().includes(stateSelect.options[i].text.toLowerCase())) {
                                    stateSelect.selectedIndex = i;
                                    stateSelect.dispatchEvent(new Event('change'));
                                    break;
                                }
                            }
                        }
                    }

                    // Generate new session token after place selection
                    state.sessionToken = generateSessionToken();

                    // Populate hidden latitude/longitude inputs if available
                    try {
                        const latInput = document.getElementById('delivery_latitude');
                        const lngInput = document.getElementById('delivery_longitude');
                        if (place.location?.lat !== undefined && place.location?.lng !== undefined) {
                            if (latInput) latInput.value = place.location.lat;
                            if (lngInput) lngInput.value = place.location.lng;
                        } else {
                            if (latInput) latInput.value = '';
                            if (lngInput) lngInput.value = '';
                        }
                    } catch (err) {
                        console.warn('Could not set delivery lat/lng inputs', err);
                    }

                    hideSuggestions();
                    console.log('[GooglePlaces] Address selected:', formattedAddress);
                    console.log('[GooglePlaces] State:', stateName);
                    console.log('[GooglePlaces] City:', cityName);

                } catch (error) {
                    console.error('[GooglePlaces] Error fetching place details:', error);
                    addressInput.value = displayText;
                    hideSuggestions();
                }
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initGooglePlaces);
            } else {
                initGooglePlaces();
            }
        })();
    </script>
</body>

</html>
