<div
    class="package-customizer min-h-screen bg-gradient-to-br from-pink-50 to-purple-50 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Package Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $package->name }}</h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">{{ $package->description }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Customization Panel -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4">
                            </path>
                        </svg>
                        Customize Your Package
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Material Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Material <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model.live="material_id" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-pink-500 focus:ring-4 focus:ring-pink-200 transition-all duration-200 bg-white dark:bg-gray-700 dark:text-white appearance-none">
                                    <option value="">Choose material</option>
                                    @foreach ($package->materials as $m)
                                        <option value="{{ $m->id }}" data-image="{{ $m->image }}">
                                            {{ $m->name }} (+{{ money($m->price_modifier) }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('material_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Font Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Font Style <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model.live="font_id" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-pink-500 focus:ring-4 focus:ring-pink-200 transition-all duration-200 bg-white dark:bg-gray-700 dark:text-white appearance-none">
                                    <option value="">Choose font</option>
                                    @foreach ($package->fonts as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}
                                            (+{{ money($f->price_modifier) }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('font_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Color Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Color <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model.live="color_id" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-pink-500 focus:ring-4 focus:ring-pink-200 transition-all duration-200 bg-white dark:bg-gray-700 dark:text-white appearance-none">
                                    <option value="">Choose color</option>
                                    @foreach ($package->colors as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->hex }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('color_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Quantity</label>
                            <div class="relative">
                                <input type="number" min="1" wire:model.live="quantity"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-pink-500 focus:ring-4 focus:ring-pink-200 transition-all duration-200 bg-white dark:bg-gray-700 dark:text-white" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 text-sm">pcs</span>
                                </div>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Personal Message <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.live="message" required
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-pink-500 focus:ring-4 focus:ring-pink-200 transition-all duration-200 bg-white dark:bg-gray-700 dark:text-white"
                                placeholder="e.g., Tunde Weds Kemi" />
                            <p class="text-sm text-gray-500 dark:text-gray-400">This will appear on your package</p>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Delivery Location / Note <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.live="location" required
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-pink-500 focus:ring-4 focus:ring-pink-200 transition-all duration-200 bg-white dark:bg-gray-700 dark:text-white"
                                placeholder="Delivery address or special instructions" />
                            @error('location')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Selection -->
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Select Events <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-3">
                                @if ($customerEvents->isEmpty())
                                    <div
                                        class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                        <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg>
                                            You don't have any active events. Please create an event first.
                                        </p>
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto">
                                        @foreach ($customerEvents as $event)
                                            <label
                                                class="flex items-center space-x-3 p-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-pink-300 dark:hover:border-pink-600 transition-colors cursor-pointer {{ in_array($event->id, $selectedEvents) ? 'border-pink-500 bg-pink-50 dark:bg-pink-900/20' : '' }}">
                                                <input type="checkbox" wire:model.live="selectedEvents"
                                                    value="{{ $event->id }}"
                                                    class="w-4 h-4 text-pink-600 border-2 border-gray-300 rounded focus:ring-pink-500 focus:ring-2">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900 dark:text-white">
                                                        {{ $event->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $event->display_subcategory }} •
                                                        {{ $event->event_date->format('M d, Y') }}
                                                    </div>
                                                    @if ($event->location)
                                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                            <svg class="w-3 h-3 inline mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                                </path>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            </svg>
                                                            {{ $event->location }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('selectedEvents')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    @error('selectedEvents.*')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Select one or more events for this
                                package</p>
                        </div>

                        <!-- Delivery Zone Selection -->
                        @if (!empty($deliveryZones))
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Delivery Zone (Optional)
                                </label>
                                <div class="relative">
                                    <select wire:model.live="delivery_zone_id"
                                        class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-pink-500 focus:ring-4 focus:ring-pink-200 transition-all duration-200 bg-white dark:bg-gray-700 dark:text-white appearance-none">
                                        <option value="">Choose delivery zone</option>
                                        @foreach ($deliveryZones as $zone)
                                            <option value="{{ $zone['id'] }}">
                                                {{ $zone['name'] }} (+{{ money($zone['price']) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('delivery_zone_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-sm text-gray-500 dark:text-gray-400">Select your delivery zone for
                                    additional delivery options</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pricing Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mt-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Order Summary</h3>
                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-300">Base Price</span>
                            <span
                                class="font-semibold text-gray-900 dark:text-white">{{ money($package->base_price ?? 0) }}</span>
                        </div>
                        @if ($material_id)
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-300">Material Upgrade</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">+{{ money(optional($package->materials->firstWhere('id', $material_id))->price_modifier ?? 0) }}</span>
                            </div>
                        @endif
                        @if ($font_id)
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-300">Font Style</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">+{{ money(optional($package->fonts->firstWhere('id', $font_id))->price_modifier ?? 0) }}</span>
                            </div>
                        @endif
                        @if ($color_id)
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-300">Color Option</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">+{{ money(optional($package->colors->firstWhere('id', $color_id))->price_modifier ?? 0) }}</span>
                            </div>
                        @endif
                        <div
                            class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-300">Quantity</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $quantity }} ×
                                {{ money($unitPrice) }}</span>
                        </div>
                        @if ($delivery_zone_id && $deliveryCost > 0)
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-300">Delivery Zone</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">+{{ money($deliveryCost) }}</span>
                            </div>
                        @endif
                        <div
                            class="flex justify-between items-center py-3 bg-pink-50 dark:bg-pink-900/20 rounded-lg px-4">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total Price</span>
                            <span
                                class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ money($totalPrice) }}</span>
                        </div>
                    </div>

                    <button wire:click="addToCart"
                        class="w-full mt-6 bg-gradient-to-r from-pink-600 to-purple-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-pink-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg cursor-pointer">
                        <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
                            </path>
                        </svg>
                        Add to Cart
                    </button>
                </div>
            </div>

            <!-- 3D Preview Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sticky top-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                        Live Preview
                    </h3>

                    <!-- 3D Preview Container -->
                    <div class="relative" wire:ignore>
                        <div id="preview-canvas"
                            class="w-full h-80 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-xl border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center overflow-hidden">
                            <!-- Three.js Canvas -->
                            <canvas id="threejs-canvas" class="w-full h-full"></canvas>

                            <!-- Loading placeholder -->
                            <div class="text-center" id="preview-loading" style="display: none;">
                                <div class="animate-pulse">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-300 font-medium">3D Preview Loading...</p>
                                <p class="text-sm text-gray-400 dark:text-gray-400 mt-1">Your custom design will appear
                                    here</p>
                            </div>

                            <!-- Texture loading indicator -->
                            <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs hidden"
                                id="texture-loading">
                                Loading texture...
                            </div>
                        </div>

                        <!-- Preview Controls -->
                        <div class="flex justify-center mt-4 space-x-2">
                            <button onclick="reset3DView()"
                                class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors duration-200">
                                Reset View
                            </button>
                        </div>

                        <!-- Selected Options Summary -->
                        <div class="mt-6 space-y-3">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Selected Options:</h4>

                            @if ($material_id)
                                <div
                                    class="flex items-center justify-between py-2 px-3 bg-pink-50 dark:bg-pink-900/20 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Material:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($package->materials->firstWhere('id', $material_id))->name }}</span>
                                </div>
                            @endif

                            @if ($font_id)
                                <div
                                    class="flex items-center justify-between py-2 px-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Font:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($package->fonts->firstWhere('id', $font_id))->name }}</span>
                                </div>
                            @endif

                            @if ($color_id)
                                <div
                                    class="flex items-center justify-between py-2 px-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Color:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($package->colors->firstWhere('id', $color_id))->name }}</span>
                                </div>
                            @endif

                            @if ($message)
                                <div class="py-2 px-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Message:</span>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                        "{{ $message }}"</p>
                                </div>
                            @endif
                        </div>

                        <!-- Optional: Server-generated preview -->
                        @if ($package->metadata['preview_hint'] ?? false)
                            <div class="mt-4">
                                <img src="{{ $package->metadata['preview_hint'] }}" alt="Package preview"
                                    class="w-full rounded-lg shadow-md" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Fonts -->
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@400;700&family=Lato:wght@400;700&family=Montserrat:wght@400;700&family=Poppins:wght@400;700&family=Playfair+Display:wght@400;700&family=Raleway:wght@400;700&family=Ubuntu:wght@400;700&family=Merriweather:wght@400;700&family=Nunito:wght@400;700&display=swap"
            rel="stylesheet">
    </div>

    <!-- Three.js for 3D Preview -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

    <script>
        // =========================
        // 3D Preview Variables
        // =========================
        let scene, camera, renderer, controls;
        let packageMesh, lidMesh, textMesh;
        let isRotating = false;
        let animationId;
        let textCanvas, textContext;

        // =========================
        // Initialize Scene
        // =========================
        function init3DPreview() {
            const container = document.getElementById('preview-canvas');
            if (!container) return console.error('Preview canvas container not found');

            // Create scene if not exists
            if (!scene) {
                scene = new THREE.Scene();
                scene.background = new THREE.Color(0xf8fafc);

                // Camera
                camera = new THREE.PerspectiveCamera(
                    75,
                    container.clientWidth / container.clientHeight,
                    0.1,
                    1000
                );
                camera.position.set(3, 3, 3);

                // Renderer (persistent canvas)
                renderer = new THREE.WebGLRenderer({
                    antialias: true,
                    alpha: true,
                    canvas: container.querySelector('canvas') || undefined
                });
                renderer.setSize(container.clientWidth, container.clientHeight);
                renderer.shadowMap.enabled = true;
                renderer.shadowMap.type = THREE.PCFSoftShadowMap;

                if (!container.querySelector('canvas')) container.appendChild(renderer.domElement);

                // Controls
                controls = new THREE.OrbitControls(camera, renderer.domElement);
                controls.enableDamping = true;
                controls.dampingFactor = 0.05;

                // Lights
                const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 0.3);
                scene.add(hemiLight);

                const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
                scene.add(ambientLight);

                const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
                directionalLight.position.set(5, 5, 5);
                directionalLight.castShadow = true;
                scene.add(directionalLight);

                // Create package
                createPackageGeometry();

                // Start animation loop
                animate();

                // Window resize handling
                window.addEventListener('resize', onWindowResize);
            } else {
                // If scene exists, just update materials
                applyCurrentMaterial();
            }
        }

        // =========================
        // Create Package Geometry
        // =========================
        function createPackageGeometry() {
            // Remove previous meshes
            if (packageMesh) disposeMesh(packageMesh);
            if (lidMesh) disposeMesh(lidMesh);
            if (textMesh) disposeMesh(textMesh);

            const boxWidth = 2.5;
            const boxHeight = 1.5;
            const boxDepth = 2.5;

            const colorHex = @json(optional($package->colors->firstWhere('id', $color_id))->hex ?? '#ffffff');
            const packageColor = new THREE.Color(colorHex);

            // Main box
            const boxMaterial = new THREE.MeshPhongMaterial({
                color: packageColor,
                shininess: 60,
                specular: 0x222222
            });

            const boxGeometry = new THREE.BoxGeometry(boxWidth, boxHeight, boxDepth);
            packageMesh = new THREE.Mesh(boxGeometry, boxMaterial);
            packageMesh.castShadow = true;
            packageMesh.receiveShadow = true;
            scene.add(packageMesh);

            // Lid
            const lidGeometry = new THREE.BoxGeometry(boxWidth + 0.1, 0.1, boxDepth + 0.1);
            const lidMaterial = new THREE.MeshPhongMaterial({
                color: packageColor,
                shininess: 80
            });
            lidMesh = new THREE.Mesh(lidGeometry, lidMaterial);
            lidMesh.position.set(0, boxHeight / 2 + 0.05, 0);
            lidMesh.castShadow = true;
            scene.add(lidMesh);

            // Ribbons
            addBoxRibbons(boxWidth, boxHeight, boxDepth);

            // Gift items inside box
            if (@json($message)) addGiftItems(boxWidth, boxHeight, boxDepth);

            // Add text to top of package
            addTextToPackage(boxWidth, boxHeight, boxDepth);

            // Apply texture after creation
            setTimeout(applyCurrentMaterial, 100);
        }

        // =========================
        // Add Box Ribbons
        // =========================
        function addBoxRibbons(width, height, depth) {
            const ribbonColor = 0xD4AF37;
            const ribbonWidth = 0.15;

            // Horizontal ribbon
            const hRibbon = new THREE.Mesh(
                new THREE.BoxGeometry(width + 0.05, ribbonWidth, depth + 0.05),
                new THREE.MeshPhongMaterial({
                    color: ribbonColor,
                    shininess: 100
                })
            );
            hRibbon.position.set(0, 0, 0);
            scene.add(hRibbon);

            // Vertical ribbon
            const vRibbon = new THREE.Mesh(
                new THREE.BoxGeometry(ribbonWidth, height + 0.05, depth + 0.05),
                new THREE.MeshPhongMaterial({
                    color: ribbonColor,
                    shininess: 100
                })
            );
            vRibbon.position.set(0, 0, 0);
            scene.add(vRibbon);
        }

        // =========================
        // Gift Items
        // =========================
        function addGiftItems(boxWidth, boxHeight, boxDepth) {
            const giftColors = [0xff6b6b, 0x4ecdc4, 0x45b7d1, 0xf9ca24, 0xf0932b];
            for (let i = 0; i < 3; i++) {
                const size = 0.2 + Math.random() * 0.3;
                const gift = new THREE.Mesh(
                    new THREE.BoxGeometry(size, size, size),
                    new THREE.MeshPhongMaterial({
                        color: giftColors[i % giftColors.length],
                        shininess: 50
                    })
                );
                gift.position.set(
                    (Math.random() - 0.5) * (boxWidth - 0.4),
                    -boxHeight / 2 + size / 2 + 0.1 + Math.random() * 0.3,
                    (Math.random() - 0.5) * (boxDepth - 0.4)
                );
                gift.castShadow = true;
                scene.add(gift);
            }
        }

        // =========================
        // Add Text to Top of Package
        // =========================
        function addTextToPackage(boxWidth, boxHeight, boxDepth) {
            const messageInput = document.querySelector('input[wire\\:model\\.live="message"]');
            const message = messageInput?.value || '';
            if (!message) return;

            const textTexture = createTextTexture(message);
            if (!textTexture) return;

            const textMaterial = new THREE.MeshBasicMaterial({
                map: textTexture,
                transparent: true,
                alphaTest: 0.1
            });

            const textGeometry = new THREE.PlaneGeometry(2, 0.5);
            textMesh = new THREE.Mesh(textGeometry, textMaterial);

            // Position on top of the box
            textMesh.position.set(0, 1.01, 0); // just above the box
            textMesh.rotation.x = -Math.PI / 2; // lay flat on top
            scene.add(textMesh);
        }

        // =========================
        // Update Text dynamically with Font
        // =========================
        function updateText() {
            const messageInput = document.querySelector('input[wire\\:model\\.live="message"]');
            const fontSelect = document.querySelector('select[wire\\:model\\.live="font_id"]');

            const message = messageInput?.value || '';
            const fontId = fontSelect?.value || null;

            if (!message) {
                if (textMesh) {
                    scene.remove(textMesh);
                    textMesh.geometry.dispose();
                    textMesh.material.dispose();
                    textMesh = null;
                }
                return;
            }

            const newTexture = createTextTexture(message, fontId);
            if (newTexture && textMesh) {
                textMesh.material.map = newTexture;
                textMesh.material.needsUpdate = true;
            } else if (newTexture && !textMesh) {
                addTextToPackage(2.5, 1.5, 2.5); // recreate text on top
            }
        }

        // =========================
        // Create Text Texture with dynamic font
        // =========================
        function createTextTexture(text, fontId = null) {
            if (!textCanvas) {
                textCanvas = document.createElement('canvas');
                textCanvas.width = 512;
                textCanvas.height = 128;
                textContext = textCanvas.getContext('2d');
            }

            // Clear canvas
            textContext.clearRect(0, 0, textCanvas.width, textCanvas.height);

            // Font
            const fontFamily = getFontFamily(fontId);
            textContext.font = `bold 48px ${fontFamily}`;
            textContext.fillStyle = '#ffffff';
            textContext.textAlign = 'center';
            textContext.textBaseline = 'middle';

            // Shadow
            textContext.shadowColor = 'rgba(0, 0, 0, 0.8)';
            textContext.shadowBlur = 4;
            textContext.shadowOffsetX = 2;
            textContext.shadowOffsetY = 2;

            // Draw text
            textContext.fillText(text, textCanvas.width / 2, textCanvas.height / 2);

            // Create texture
            const texture = new THREE.CanvasTexture(textCanvas);
            texture.needsUpdate = true;
            return texture;
        }

        // =========================
        // Get font family dynamically
        // =========================
        function getFontFamily(fontId) {
            const fonts = @json($package->fonts); // server-side font list

            const font = fonts.find(f => f.id == fontId);

            if (font?.google_font_family) return font.google_font_family;

            const fontMap = {
                'Arial': 'Arial, sans-serif',
                'Times New Roman': 'Times New Roman, serif',
                'Helvetica': 'Helvetica, Arial, sans-serif',
                'Georgia': 'Georgia, serif',
                'Verdana': 'Verdana, sans-serif',
                'Courier New': 'Courier New, monospace',
                'Impact': 'Impact, sans-serif',
                'Comic Sans MS': 'Comic Sans MS, cursive'
            };

            return fontMap[font?.name] || 'Arial, sans-serif';
        }

        // =========================
        // Apply Current Material / Texture
        // =========================
        function applyCurrentMaterial() {
            if (!packageMesh) return;

            const materialSelect = document.querySelector('select[wire\\:model\\.live="material_id"]');
            const selectedOption = materialSelect?.options[materialSelect.selectedIndex];
            const materialImage = selectedOption?.getAttribute('data-image');
            const colorHex = @json(optional($package->colors->firstWhere('id', $color_id))->hex ?? '#ffffff');

            const baseMaterial = new THREE.MeshPhongMaterial({
                color: colorHex,
                shininess: 60,
                specular: 0x222222
            });

            packageMesh.material = baseMaterial;
            lidMesh.material.color.set(colorHex);

            if (!materialImage) return;

            const loader = new THREE.TextureLoader();
            const imageUrl = `/storage/${materialImage}?t=${Date.now()}`;
            const loadingIndicator = document.getElementById('texture-loading');
            if (loadingIndicator) loadingIndicator.classList.remove('hidden');

            loader.load(
                imageUrl,
                texture => {
                    texture.wrapS = THREE.RepeatWrapping;
                    texture.wrapT = THREE.RepeatWrapping;
                    texture.repeat.set(1, 1);
                    texture.needsUpdate = true;

                    packageMesh.material.map = texture;
                    packageMesh.material.needsUpdate = true;

                    lidMesh.material.map = texture.clone();
                    lidMesh.material.needsUpdate = true;

                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    renderer.render(scene, camera);
                },
                undefined,
                err => {
                    console.error('Error loading texture:', err);
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            );
        }

        // =========================
        // Utility: Dispose Mesh
        // =========================
        function disposeMesh(mesh) {
            if (!mesh) return;
            scene.remove(mesh);
            if (mesh.geometry) mesh.geometry.dispose();
            if (mesh.material) mesh.material.dispose();
        }

        // =========================
        // Animation Loop
        // =========================
        function animate() {
            animationId = requestAnimationFrame(animate);
            if (isRotating && packageMesh) packageMesh.rotation.y += 0.01;
            controls.update();
            renderer.render(scene, camera);
        }

        // =========================
        // Window Resize
        // =========================
        function onWindowResize() {
            const container = document.getElementById('preview-canvas');
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        }

        // =========================
        // View Controls
        // =========================
        function reset3DView() {
            if (controls) controls.reset();
        }

        // =========================
        // Livewire Update Listener
        // =========================
        if (window.Livewire) {
            window.Livewire.hook('message.processed', () => {
                applyCurrentMaterial();
                updateText();
            });
        }

        // =========================
        // Event Listeners for Live Updates
        // =========================
        const messageInput = document.querySelector('input[wire\\:model\\.live="message"]');
        if (messageInput) messageInput.addEventListener('input', updateText);

        const fontSelect = document.querySelector('select[wire\\:model\\.live="font_id"]');
        if (fontSelect) fontSelect.addEventListener('change', updateText);

        // =========================
        // Material Dropdown Listener
        // =========================
        function setupMaterialDropdownListener() {
            const select = document.querySelector('select[wire\\:model\\.live="material_id"]');
            if (!select) return;
            select.addEventListener('change', applyCurrentMaterial);
        }
        setupMaterialDropdownListener();

        // =========================
        // Initialize on DOM Ready
        // =========================
        document.addEventListener('DOMContentLoaded', () => setTimeout(init3DPreview, 100));
    </script>
