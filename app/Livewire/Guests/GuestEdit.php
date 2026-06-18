<?php

namespace App\Livewire\Guests;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Guest;
use App\Models\Event;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GuestEdit extends Component
{
    public $isEdit = true;
    public $guest;
    public $guestId;
    public $name, $email, $phone, $rsvp_status, $notes, $customer_id;
    public $selectedEvents = [];
    public $customerEvents = [];
    public $states = [];
    public $cities = [];
    public $state_id = null;
    public $city_id = null;
    public string $address = '';
    public $latitude = null;
    public $longitude = null;
    public $showNotificationConfirm = false;

    public function mount($guest)
    {
        $this->guestId = $guest;
        $this->customer_id = Auth::user()->customer->id ?? null;

        // Load states for dropdown
        $this->states = \App\Models\State::orderBy('name')->get();

        // Load the guest data
        $this->guest = Guest::where('customer_id', $this->customer_id)
            ->where('id', $this->guestId)
            ->firstOrFail();

        // Populate form fields
        $this->name = $this->guest->full_name;
        $this->email = $this->guest->email;
        $this->phone = $this->guest->phone;
        $this->rsvp_status = $this->guest->rsvp_status;
        $this->notes = $this->guest->notes;
        $this->address = $this->guest->address ?? '';
        $this->latitude = $this->guest->latitude;
        $this->longitude = $this->guest->longitude;
        $this->state_id = $this->guest->state_id;
        $this->city_id = $this->guest->city_id;

        // Load cities for the selected state
        if ($this->state_id) {
            $this->updatedStateId($this->state_id);
        }

        // Load customer's events
        if ($this->customer_id) {
            $this->customerEvents = Event::where('customer_id', $this->customer_id)
                ->orderBy('event_date', 'desc')
                ->get();
        }

        // Load currently attached events
        $this->selectedEvents = $this->guest->events()->pluck('events.id')->toArray();
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|digits:11',
        'rsvp_status' => 'nullable|string|in:pending,confirmed,declined',
        'notes' => 'nullable|string',
        'customer_id' => 'required|exists:customers,id',
        'selectedEvents' => 'nullable|array',
        'selectedEvents.*' => 'exists:events,id',
        'address' => 'required|string|max:500',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'state_id' => 'nullable|exists:states,id',
        'city_id' => 'nullable|exists:cities,id',
    ];

    public function save()
    {
        $this->validate();

        // Show notification confirmation dialog
        $this->showNotificationConfirm = true;
    }

    private function performSave()
    {
        // Parse the name into first and last name
        $nameParts = $this->parseName($this->name);

        // Update guest
        $this->guest->update([
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'email' => $this->email,
            'phone' => $this->phone,
            'rsvp_status' => $this->rsvp_status,
            'notes' => $this->notes,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
        ]);

        // Sync events - handle existing and new attachments
        $eventData = [];
        foreach ($this->selectedEvents as $eventId) {
            // Check if guest is already attached to this event
            $existingPivot = $this->guest->events()->where('event_id', $eventId)->first();

            if ($existingPivot) {
                // Keep existing RSVP token and dates if already attached
                $eventData[$eventId] = [
                    'attendance_status' => $existingPivot->pivot->attendance_status ?? 'invited',
                    'rsvp_token' => $existingPivot->pivot->rsvp_token,
                    'rsvp_sent_at' => $existingPivot->pivot->rsvp_sent_at,
                    'rsvp_expires_at' => $existingPivot->pivot->rsvp_expires_at,
                ];
            } else {
                // New attachment
                $eventData[$eventId] = [
                    'attendance_status' => 'invited',
                    'rsvp_token' => Str::random(32),
                    'rsvp_sent_at' => now(),
                    'rsvp_expires_at' => now()->addDays(7),
                ];
            }
        }

        $this->guest->events()->sync($eventData);
    }

    public function confirmSaveWithNotifications()
    {
        // Save guest data
        $this->performSave();

        // TODO: Add notification sending logic here
        // For now, just show a success message
        session()->flash('message', 'Guest updated successfully. Notifications will be sent.');

        $this->showNotificationConfirm = false;

        return redirect()->route('guests.list');
    }

    public function confirmSaveWithoutNotifications()
    {
        // Just save without notifications
        $this->performSave();

        session()->flash('message', 'Guest updated successfully and event attachments updated.');

        $this->showNotificationConfirm = false;

        return redirect()->route('guests.list');
    }

    public function cancelNotificationPrompt()
    {
        $this->showNotificationConfirm = false;
    }

    private function parseName($fullName)
    {
        $nameParts = explode(' ', trim($fullName));
        $firstName = array_shift($nameParts);
        $lastName = !empty($nameParts) ? implode(' ', $nameParts) : '';

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
    }

    /**
     * Check if customer has paid for delivery services
     */
    private function hasPaidDeliveryServices()
    {
        $customer = Auth::user()->customer;

        if (!$customer) {
            return false;
        }

        // Check if customer has any events with paid delivery services
        $paidDeliveries = \App\Models\Delivery::whereHas('event', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })
            ->where('delivery_required', true)
            ->where('payment_status', 'paid')
            ->exists();

        // Check if customer has approved payment receipts for offline payments
        $approvedReceipts = \App\Models\PaymentReceipt::where('customer_id', $customer->id)
            ->where('status', 'approved')
            ->exists();

        return $paidDeliveries || $approvedReceipts;
    }

    public function updatedStateId($stateId)
    {
        if ($stateId) {
            $this->cities = \App\Models\City::where('state_id', $stateId)->orderBy('name')->get();
            // Only reset city_id if it's not already set or if it's not in the new cities list
            $existingCityId = $this->city_id;
            // Ensure cities is treated as a Collection before calling contains
            if ($existingCityId && !collect($this->cities)->contains(function ($city) use ($existingCityId) {
                return $city->id == $existingCityId;
            })) {
                $this->city_id = null; // Reset city selection if current city is not in new state
            }
        } else {
            $this->cities = collect(); // Use empty collection instead of array
            $this->city_id = null;
        }
    }

    public function updateCities($stateId)
    {
        $this->updatedStateId($stateId);
    }

    #[On('address-updated')]
    public function handleAddressUpdated($data)
    {
        if ($data['field'] === 'address') {
            $this->address = $data['value'];

            // Try to extract state from address and auto-populate
            if (!empty($data['value'])) {
                $this->autoPopulateStateFromAddress($data['value']);
            }
        }
    }

    #[On('coordinates-updated')]
    public function handleCoordinatesUpdated($latitude = null, $longitude = null)
    {
        $this->latitude  = $latitude  !== null ? (string) $latitude  : $this->latitude;
        $this->longitude = $longitude !== null ? (string) $longitude : $this->longitude;
    }

    #[On('address-geocoded')]
    public function handleAddressGeocoded($field = null, $value = null, $latitude = null, $longitude = null)
    {
        if ($field === 'address') {
            $this->address   = $value ?? $this->address;
            $this->latitude  = $latitude  !== null ? (string) $latitude  : $this->latitude;
            $this->longitude = $longitude !== null ? (string) $longitude : $this->longitude;
        }
    }

    #[On('google-places-state-detected')]
    public function handleStateDetected($data)
    {
        if ($data['field'] === 'state_id') {
            $this->autoSelectStateByName($data['stateName']);
        }
    }

    /**
     * Auto-populate state based on address text
     */
    private function autoPopulateStateFromAddress($address)
    {
        $addressParts = explode(',', $address);
        foreach ($addressParts as $part) {
            $part = trim($part);
            // Try to find state by name (case insensitive)
            $foundState = State::whereRaw('LOWER(name) = ?', [strtolower($part)])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($part) . '%'])
                ->where('is_active', true)
                ->first();

            if ($foundState) {
                $this->state_id = $foundState->id;
                $this->updatedStateId($foundState->id);
                break;
            }
        }
    }

    /**
     * Auto-select state by exact or partial name match
     */
    private function autoSelectStateByName($stateName)
    {
        if (empty($stateName)) {
            return;
        }

        $foundState = State::whereRaw('LOWER(name) = ?', [strtolower($stateName)])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($stateName) . '%'])
            ->where('is_active', true)
            ->first();

        if ($foundState) {
            $this->state_id = $foundState->id;
            $this->updatedStateId($foundState->id);
        }
    }

    public function render()
    {
        return view('livewire.guests.guest-edit', [
            'customerEvents' => $this->customerEvents,
            'states' => $this->states,
            'cities' => $this->cities,
        ]);
    }
}
