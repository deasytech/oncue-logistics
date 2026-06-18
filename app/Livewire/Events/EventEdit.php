<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Category;
use App\Models\FabricType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EventEdit extends Component
{
    use WithFileUploads;
    public Event $event;

    public $name;
    public $category_id;
    public $subcategory_id;
    public $custom_subcategory;
    public $location;
    public $estimated_number_of_guest;
    public $event_date;
    public $aso_ebi_color;
    public $logo;
    public $description;
    public $notes;

    public $categories = [];
    public $subcategories = [];
    public $fabricTypes = [];
    public $selectedFabricTypes = [];
    public $fabricPrices = [];
    public $isCustomCategory = false;

    protected $messages = [
        'name.required' => 'Event name is required.',
        'category_id.required' => 'Please select a category.',
        'subcategory_id.required' => 'Please select a sub-category.',
        'location.required' => 'Event location is required.',
        'event_date.required' => 'Please select an event date.',
        'estimated_number_of_guest.required' => 'Estimated number of guests is required.',
        'estimated_number_of_guest.min' => 'Minimum :min guests required.',
    ];

    /**
     * Get the minimum number of guests based on the selected subcategory
     */
    public function getMinimumGuests(): int
    {
        // Subcategories that require minimum 50 guests
        $highMinimumSubcategories = [
            'White Wedding',
            'Traditional Engagement',
            'Introduction',
        ];

        // Check if we have a selected subcategory
        if ($this->isCustomCategory) {
            // For custom subcategories, check if it matches any of the high minimum names
            if ($this->custom_subcategory && in_array($this->custom_subcategory, $highMinimumSubcategories, true)) {
                return 50;
            }
        } else {
            // For regular subcategories, get the subcategory name
            if ($this->subcategory_id) {
                $subcategory = collect($this->subcategories)->firstWhere('id', $this->subcategory_id);
                if ($subcategory && in_array($subcategory->name, $highMinimumSubcategories, true)) {
                    return 50;
                }
            }
        }

        // Default minimum for all other subcategories
        return 5;
    }

    protected function rules(): array
    {
        $minGuests = $this->getMinimumGuests();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'location' => ['required', 'string', 'max:255'],
            'estimated_number_of_guest' => ['required', 'integer', 'min:' . $minGuests],
            'event_date' => ['required', 'date'],
            'aso_ebi_color' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'max:2048'], // Max 2MB
            'description' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        // If it's a custom category, require custom_subcategory instead of subcategory_id
        if ($this->isCustomCategory) {
            $rules['custom_subcategory'] = ['required', 'string', 'max:255'];
            $rules['subcategory_id'] = ['nullable'];
        } else {
            $rules['subcategory_id'] = ['required', 'exists:categories,id'];
            $rules['custom_subcategory'] = ['nullable'];
        }

        return $rules;
    }

    public function mount(Event $event): void
    {
        $this->event = $event;

        // Fill basic properties manually to ensure custom_subcategory is loaded
        $this->name = $event->name;
        $this->category_id = $event->category_id;
        $this->subcategory_id = $event->subcategory_id;
        $this->custom_subcategory = $event->custom_subcategory;
        $this->location = $event->location;
        $this->estimated_number_of_guest = $event->estimated_number_of_guest;
        $this->aso_ebi_color = $event->aso_ebi_color;
        $this->description = $event->description;
        $this->notes = $event->notes;

        // Format the date properly for HTML date input
        $this->event_date = $event->event_date ? $event->event_date->format('Y-m-d') : null;

        $this->categories = Category::whereNull('parent_id')->get();
        $this->fabricTypes = FabricType::active()->get();

        // Load existing fabric types for this event
        $existingFabricTypes = $event->fabricTypes;
        foreach ($existingFabricTypes as $fabricType) {
            $this->selectedFabricTypes[] = $fabricType->id;
            $this->fabricPrices[$fabricType->id] = $fabricType->pivot->custom_price;
        }

        // Initialize fabric prices for all fabric types
        foreach ($this->fabricTypes as $fabricType) {
            if (!isset($this->fabricPrices[$fabricType->id])) {
                $this->fabricPrices[$fabricType->id] = $fabricType->base_price;
            }
        }

        // Check if the current category is "Custom" BEFORE calling updatedCategoryId
        $category = Category::find($this->category_id);
        $this->isCustomCategory = $category && strtolower($category->name) === 'custom';

        // Now call updatedCategoryId with the correct isCustomCategory value
        $this->updatedCategoryId($this->category_id);
    }

    public function updatedCategoryId($value): void
    {
        $this->subcategories = Category::where('parent_id', $value)->get();

        // Check if the selected category is "Custom"
        $category = Category::find($value);
        $isNowCustomCategory = $category && strtolower($category->name) === 'custom';

        // Only reset subcategory fields if switching between custom and non-custom
        if ($this->isCustomCategory !== $isNowCustomCategory) {
            $this->subcategory_id = null;
            $this->custom_subcategory = null;
        }

        $this->isCustomCategory = $isNowCustomCategory;

        if (!$this->isCustomCategory && !collect($this->subcategories)->contains('id', $this->subcategory_id)) {
            $this->subcategory_id = null;
        }
    }

    public function save()
    {
        $this->validate();

        // Handle logo upload
        if ($this->logo) {
            // Delete old logo if exists
            if ($this->event->logo) {
                Storage::disk('public')->delete($this->event->logo);
            }
            $logoPath = $this->logo->store('events/logos', 'public');
        } else {
            $logoPath = $this->event->logo;
        }

        $this->event->update([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->isCustomCategory ? null : $this->subcategory_id,
            'custom_subcategory' => $this->isCustomCategory ? $this->custom_subcategory : null,
            'location' => $this->location,
            'estimated_number_of_guest' => $this->estimated_number_of_guest,
            'event_date' => $this->event_date,
            'aso_ebi_color' => $this->aso_ebi_color,
            'logo' => $logoPath,
            'description' => $this->description,
            'notes' => $this->notes,
        ]);

        // Sync fabric types with custom prices
        if (!empty($this->selectedFabricTypes)) {
            $fabricData = [];
            foreach ($this->selectedFabricTypes as $fabricTypeId) {
                $fabricData[$fabricTypeId] = [
                    'custom_price' => $this->fabricPrices[$fabricTypeId] ?? 0
                ];
            }
            $this->event->fabricTypes()->sync($fabricData);
        } else {
            // Detach all fabric types if none selected
            $this->event->fabricTypes()->detach();
        }

        session()->flash('message', 'Event updated successfully.');
        return redirect()->route('events.list');
    }

    public function render()
    {
        return view('livewire.events.event-edit', [
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
            'isCustomCategory' => $this->isCustomCategory,
            'fabricTypes' => $this->fabricTypes,
        ]);
    }
}
