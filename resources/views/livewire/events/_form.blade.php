<div title="{{ $isEdit ? __('Edit Event') : __('Create Event') }}">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                {{-- <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $isEdit ? 'Edit Event' : 'Create Event' }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $isEdit ? 'Update event information' : 'Add a new event to your system' }}
                </p> --}}
            </div>

            <a href="{{ route('events.list') }}" wire:navigate
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700 font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Events
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

                <!-- General Error Display -->
                @if ($errors->has('general'))
                    <div
                        class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-900 dark:border-red-700 dark:text-red-300">
                        {{ $errors->first('general') }}
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Event Name -->
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Event Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.live="name" id="name"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                placeholder="Enter event name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="category_id" id="category_id"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subcategory -->
                        <div>
                            <label for="subcategory_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subcategory <span class="text-red-500">*</span>
                            </label>

                            @if ($isCustomCategory)
                                <!-- Custom subcategory input -->
                                <input type="text" wire:model.live="custom_subcategory" id="custom_subcategory"
                                    class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('custom_subcategory') border-red-500 @enderror"
                                    placeholder="Enter custom subcategory">
                                @error('custom_subcategory')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            @else
                                <!-- Regular subcategory dropdown -->
                                <select wire:model.live="subcategory_id" id="subcategory_id"
                                    class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subcategory_id') border-red-500 @enderror"
                                    {{ empty($subcategories) ? 'disabled' : '' }}>
                                    <option value="">Select Subcategory</option>
                                    @foreach ($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                    @endforeach
                                </select>
                                @error('subcategory_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.live="location" id="location"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                                placeholder="Enter event location">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estimated Guests -->
                        <div>
                            <label for="estimated_number_of_guest"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Estimated Guests <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model.live="estimated_number_of_guest"
                                id="estimated_number_of_guest" min="{{ $this->getMinimumGuests() }}"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('estimated_number_of_guest') border-red-500 @enderror"
                                placeholder="Enter guest estimate">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Minimum: {{ $this->getMinimumGuests() }} guests
                            </p>
                            @error('estimated_number_of_guest')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Date -->
                        <div>
                            <label for="event_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Event Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model.live="event_date" id="event_date"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('event_date') border-red-500 @enderror">
                            @error('event_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Aso Ebi Color -->
                        <div>
                            <label for="aso_ebi_color"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Aso Ebi Color (Optional)
                            </label>
                            <input type="text" wire:model.live="aso_ebi_color" id="aso_ebi_color"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('aso_ebi_color') border-red-500 @enderror"
                                placeholder="Enter Aso Ebi Color">
                            @error('aso_ebi_color')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Logo -->
                        <div>
                            <label for="logo"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Event Logo (Optional - displayed on RSVP form)
                            </label>
                            <input type="file" wire:model="logo" id="logo" accept="image/*"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('logo') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Upload an image logo to display on the RSVP form (max 2MB)
                            </p>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            @if ($isEdit && $event->logo)
                                <div class="mt-2">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Current Logo:</p>
                                    <img src="{{ Storage::url($event->logo) }}" alt="Event Logo"
                                        class="h-16 w-auto object-contain rounded border">
                                </div>
                            @endif
                        </div>

                        <!-- Event Description -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Event Description (Optional - displayed on RSVP form)
                            </label>
                            <textarea wire:model.live="description" id="description" rows="3"
                                class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                placeholder="Enter a short description to display on the RSVP form"></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Fabric Types Section -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Fabric Types</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select the fabric types for this event
                            and set custom prices if needed.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($fabricTypes as $fabricType)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <input type="checkbox" wire:model.live="selectedFabricTypes"
                                            value="{{ $fabricType->id }}" id="fabric_{{ $fabricType->id }}"
                                            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <div class="flex-1">
                                            <label for="fabric_{{ $fabricType->id }}"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $fabricType->name }}
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $fabricType->description }}</p>

                                            <div class="mt-2">
                                                <label for="price_{{ $fabricType->id }}"
                                                    class="block text-xs font-medium text-gray-600 dark:text-gray-400">
                                                    Custom Price (₦)
                                                </label>
                                                <input type="number"
                                                    wire:model.live="fabricPrices.{{ $fabricType->id }}"
                                                    id="price_{{ $fabricType->id }}" step="0.01" min="0"
                                                    class="mt-1 block w-full px-2 py-1 text-xs border rounded bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 @error('fabricPrices.' . $fabricType->id) border-red-500 @enderror"
                                                    {{ !in_array($fabricType->id, $selectedFabricTypes) ? 'disabled' : '' }}>
                                                @error('fabricPrices.' . $fabricType->id)
                                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                                        {{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if (empty($fabricTypes))
                            <p class="text-sm text-gray-500 dark:text-gray-400">No fabric types available. Please
                                contact support.</p>
                        @endif
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2 lg:col-span-3">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea wire:model.live="notes" id="notes" rows="3"
                            class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                            placeholder="Enter additional details"></textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('events.list') }}" wire:navigate
                            class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700 font-medium rounded-lg transition-colors duration-200 cursor-pointer">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 cursor-pointer">
                            {{ $isEdit ? 'Update Event' : 'Create Event' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
