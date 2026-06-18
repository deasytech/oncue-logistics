<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Category;
use App\Models\FabricType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EventCreate extends Component
{
    use WithFileUploads;
    public string $name = '';
    public ?int $category_id = null;
    public ?int $subcategory_id = null;
    public ?string $custom_subcategory = null;
    public string $location = '';
    public ?int $estimated_number_of_guest = null;
    public ?string $event_date = null;
    public string $aso_ebi_color = '';
    public $logo = null;
    public string $description = '';
    public string $notes = '';

    public $categories = [];
    public $subcategories = [];
    public $fabricTypes = [];
    public $selectedFabricTypes = [];
    public $fabricPrices = [];
    public $showSuccessModal = false;
    public $createdEventId = null;
    public $isCustomCategory = false;

    protected $messages = [
        'name.required' => 'Event name is required.',
        'category_id.required' => 'Please select a category.',
        'subcategory_id.required' => 'Please select a sub-category.',
        'location.required' => 'Event location is required.',
        'event_date.required' => 'Please select an event date.',
        'event_date.after_or_equal' => 'Event date must be at least 7 days from today.',
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
            'event_date' => ['required', 'date', 'after_or_equal:' . now()->addDays(7)->format('Y-m-d')],
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

    public function mount(): void
    {
        // Check if user is authenticated and has a customer record
        if (!Auth::check()) {
            abort(403, 'Please login to create events.');
        }

        // Note: Customer record will be created automatically if missing during event creation

        $this->categories = Category::whereNull('parent_id')->get();
        $this->fabricTypes = FabricType::active()->get();

        // Initialize fabric prices with base prices
        foreach ($this->fabricTypes as $fabricType) {
            $this->fabricPrices[$fabricType->id] = $fabricType->base_price;
        }
    }

    public function updatedCategoryId($value): void
    {
        $this->subcategories = Category::where('parent_id', $value)->get();
        $this->subcategory_id = null;
        $this->custom_subcategory = null;

        // Check if the selected category is "Custom"
        $category = Category::find($value);
        $this->isCustomCategory = $category && strtolower($category->name) === 'custom';
    }

    public function save()
    {
        Log::info('Event save method called');
        Log::info('Form data:', [
            'name' => $this->name,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'location' => $this->location,
            'estimated_number_of_guest' => $this->estimated_number_of_guest,
            'event_date' => $this->event_date,
        ]);

        try {
            $this->validate();
            Log::info('Validation passed');

            // Check if user has a customer record
            if (!Auth::user()->customer) {
                Log::warning('No customer record found for user ID: ' . Auth::id() . '. Creating customer record.');

                // Create a basic customer record for the user
                $customer = \App\Models\Customer::create([
                    'user_id' => Auth::id(),
                    'first_name' => Auth::user()->name ?? 'Unknown',
                    'last_name' => '',
                    'email' => Auth::user()->email,
                    'is_active' => true,
                ]);

                Log::info('Customer record created with ID: ' . $customer->id);
            }

            Log::info('Creating event with customer ID: ' . Auth::user()->customer->id);

            // Handle logo upload
            $logoPath = null;
            if ($this->logo) {
                $logoPath = $this->logo->store('events/logos', 'public');
                Log::info('Logo uploaded to: ' . $logoPath);
            }

            $event = Event::create([
                'name' => $this->name,
                'customer_id' => Auth::user()->customer->id,
                'category_id' => $this->category_id,
                'subcategory_id' => $this->isCustomCategory ? null : $this->subcategory_id,
                'custom_subcategory' => $this->isCustomCategory ? $this->custom_subcategory : null,
                'location' => $this->location,
                'estimated_number_of_guest' => (string) $this->estimated_number_of_guest,
                'event_date' => $this->event_date,
                'aso_ebi_color' => $this->aso_ebi_color,
                'logo' => $logoPath,
                'description' => $this->description,
                'notes' => $this->notes,
            ]);

            Log::info('Event created successfully with ID: ' . $event->id);

            // Attach selected fabric types with custom prices
            if (!empty($this->selectedFabricTypes)) {
                $fabricData = [];
                foreach ($this->selectedFabricTypes as $fabricTypeId) {
                    $fabricData[$fabricTypeId] = [
                        'custom_price' => $this->fabricPrices[$fabricTypeId] ?? 0
                    ];
                }
                $event->fabricTypes()->attach($fabricData);
            }

            $this->createdEventId = $event->id;
            $this->showSuccessModal = true;

            Log::info('Modal triggered with event ID: ' . $this->createdEventId);
            Log::info('showSuccessModal set to: ' . ($this->showSuccessModal ? 'true' : 'false'));

            // Reset form fields except modal-related properties
            $this->reset([
                'name',
                'category_id',
                'subcategory_id',
                'custom_subcategory',
                'location',
                'estimated_number_of_guest',
                'event_date',
                'aso_ebi_color',
                'description',
                'notes',
                'selectedFabricTypes',
                'fabricPrices'
            ]);
        } catch (\Exception $e) {
            Log::error('Event creation failed: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            $this->addError('general', 'Failed to create event. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function navigateToGuestListUpload()
    {
        return redirect()->route('guests.list');
    }

    public function navigateToDeliveryServices()
    {
        return redirect()->route('delivery.services', ['event_id' => $this->createdEventId]);
    }

    public function navigateToPackageSelection()
    {
        return redirect()->route('packages.list');
    }

    public function closeModal()
    {
        $this->showSuccessModal = false;
        return redirect()->route('events.list');
    }

    public function render()
    {
        Log::info('Rendering EventCreate component - showSuccessModal: ' . ($this->showSuccessModal ? 'true' : 'false'));
        Log::info('Created Event ID: ' . ($this->createdEventId ?? 'null'));

        return view('livewire.events.event-create', [
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
            'isCustomCategory' => $this->isCustomCategory,
            'fabricTypes' => $this->fabricTypes,
        ]);
    }
}
