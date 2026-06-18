<?php

namespace App\Livewire\Packages;

use App\Models\Package;
use App\Models\PackageCustomization;
use App\Services\DeliveryZoneService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PackageCustomizer extends Component
{
    public ?int $packageId;
    public $package;

    public ?int $material_id = null;
    public ?int $font_id = null;
    public ?int $color_id = null;
    public ?int $delivery_zone_id = null;

    public $message = '';
    public $location = '';
    public $quantity = 1;
    public $selectedEvents = []; // Array to store selected event IDs
    public $deliveryZones = []; // Array to store delivery zones from API
    public $selectedCategory = null; // For category radio selection
    public $availableZones = []; // Zones within selected category

    // computed
    public $unitPrice = 0;
    public $totalPrice = 0;
    public $deliveryCost = 0;

    protected $rules = [
        'material_id' => 'required|exists:package_materials,id',
        'font_id' => 'required|exists:package_fonts,id',
        'color_id' => 'required|exists:package_colors,id',
        'message' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
        'delivery_zone_id' => 'nullable|integer',
        'selectedEvents' => 'required|array|min:1',
        'selectedEvents.*' => 'required|integer|exists:events,id',
    ];

    public function mount($packageId)
    {
        $this->packageId = $packageId;
        $this->package = Package::with(['materials', 'fonts', 'colors'])->findOrFail($packageId);

        // Fetch delivery zones from external API
        $deliveryZoneService = new DeliveryZoneService();
        $this->deliveryZones = $deliveryZoneService->getZonesByCategory();

        $this->recalculatePrices();
    }

    public function updatedSelectedCategory()
    {
        // When category changes, update available zones and reset selected zone
        $this->delivery_zone_id = null;
        $this->updateAvailableZones();
        $this->recalculatePrices();
    }

    public function updatedDeliveryZoneId()
    {
        // When zone changes, recalc prices
        $this->recalculatePrices();
    }

    public function updated($property)
    {
        // whenever an option changes recalc prices (except category and zone which we handle separately)
        if (!in_array($property, ['selectedCategory', 'delivery_zone_id'])) {
            $this->recalculatePrices();
        }
        $this->validateOnly($property);
    }

    protected function updateAvailableZones()
    {
        $this->availableZones = [];

        if ($this->selectedCategory) {
            foreach ($this->deliveryZones as $category) {
                if ($category['category'] === $this->selectedCategory) {
                    $this->availableZones = $category['zones'];
                    break;
                }
            }
        }
    }

    protected function recalculatePrices()
    {
        $base = $this->package->base_price ?? 0;
        $materialModifier = optional($this->package->materials->firstWhere('id', $this->material_id))->price_modifier ?? 0;
        $fontModifier = optional($this->package->fonts->firstWhere('id', $this->font_id))->price_modifier ?? 0;
        $colorModifier = optional($this->package->colors->firstWhere('id', $this->color_id))->price_modifier ?? 0;

        // Calculate delivery cost - only delivery zone costs (delivery services removed)
        $deliveryZoneCost = 0;

        if ($this->delivery_zone_id) {
            $deliveryZoneService = new DeliveryZoneService();
            $deliveryZoneCost = $deliveryZoneService->getDeliveryCost($this->delivery_zone_id);
        }

        $this->deliveryCost = $deliveryZoneCost;

        $this->unitPrice = (float) $base + (float) $materialModifier + (float) $fontModifier + (float) $colorModifier;
        $this->totalPrice = ($this->unitPrice * max(1, (int)$this->quantity)) + $this->deliveryCost;
    }

    public function addToCart()
    {
        $this->validate();

        $customerId = Auth::user()->customer->id ?? null;
        if (!$customerId) {
            $this->addError('customer', 'You do not have an associated customer account.');
            return;
        }

        $customization = PackageCustomization::create([
            'package_id' => $this->packageId,
            'customer_id' => $customerId,
            'material_id' => $this->material_id,
            'font_id' => $this->font_id,
            'color_id' => $this->color_id,
            'message' => $this->message,
            'location' => $this->location,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'total_price' => $this->totalPrice,
            'status' => 'in_cart',
            'meta' => [
                'delivery_zone_id' => $this->delivery_zone_id,
            ],
            'delivery_service_id' => null, // Delivery services removed, only zones remain
        ]);

        // Attach selected events to the customization
        $customization->events()->attach($this->selectedEvents);

        session()->flash('message', 'Added to cart successfully.');
        return redirect()->route('cart.summary');
    }

    public function render()
    {
        $hexColor = null;
        if ($this->color_id) {
            $hexColor = optional($this->package->colors->firstWhere('id', $this->color_id))->hex;
        }

        // Get material image if available
        $materialImage = null;
        if ($this->material_id) {
            $material = $this->package->materials->firstWhere('id', $this->material_id);
            $materialImage = $material ? $material->image : null;
        }

        // Delivery services removed, only zones remain
        $deliveryServices = collect();

        // Get customer's active events
        $customerEvents = collect();
        $customerId = Auth::user()->customer->id ?? null;
        if ($customerId) {
            $customerEvents = \App\Models\Event::where('customer_id', $customerId)
                ->where('is_active', true)
                ->orderBy('event_date', 'desc')
                ->get();
        }

        return view('livewire.packages.package-customizer', [
            'package' => $this->package,
            'selectedColor' => $this->color_id,
            'hexColor' => $hexColor,
            'materialImage' => $materialImage,
            'deliveryServices' => $deliveryServices,
            'deliveryZones' => $this->deliveryZones,
            'customerEvents' => $customerEvents,
            'selectedCategory' => $this->selectedCategory,
            'availableZones' => $this->availableZones,
        ]);
    }
}
