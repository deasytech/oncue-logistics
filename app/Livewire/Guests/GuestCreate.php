<?php

namespace App\Livewire\Guests;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Guest;
use App\Models\Event;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Mail\GuestRsvpInviteMail;
use Illuminate\Support\Facades\Mail;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Http;

class GuestCreate extends Component
{
    public $isEdit = false;
    public $name, $email, $phone, $notes, $customer_id;
    public $state_id, $city_id;
    public string $address = '';
    public $latitude = null;
    public $longitude = null;
    public $rsvp_status = '';
    public $selectedEvents = [];
    public $showNotificationConfirm = false;
    /** @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|null */
    public $customerEvents;
    public $states = [];
    public $cities = [];

    public function mount()
    {
        $this->rsvp_status = 'pending';
        $this->customer_id = Auth::user()->customer->id ?? null;
        $this->customerEvents = collect();

        // Load states for dropdown
        $this->states = \App\Models\State::where('is_active', true)->get();

        // Load customer's events for the dropdown
        if ($this->customer_id) {
            $this->customerEvents = Event::where('customer_id', $this->customer_id)
                ->orderBy('event_date', 'desc')
                ->get();
        }

        // Pre-select event if event_id is provided in URL
        if (request()->has('event_id') && $this->customerEvents->isNotEmpty()) {
            $eventId = request()->get('event_id');
            if ($this->customerEvents->contains(function ($event) use ($eventId) {
                return $event->id == $eventId;
            })) {
                $this->selectedEvents = [$eventId];
            }
        }
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|digits:11',
        'address' => 'required|string|max:500',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'state_id' => 'nullable|exists:states,id',
        'city_id' => 'nullable|exists:cities,id',
        // 'rsvp_status' => 'nullable|string|in:pending,confirmed,declined',
        // 'notes' => 'nullable|string',
        'customer_id' => 'required|exists:customers,id',
        'selectedEvents' => 'nullable|array',
        'selectedEvents.*' => 'exists:events,id',
    ];

    public function updatedStateId($value)
    {
        $this->city_id = null;
        $this->cities = [];

        if ($value) {
            $this->cities = \App\Models\City::where('state_id', $value)
                ->where('is_active', true)
                ->get();
        }
    }

    public function updateCities($stateId)
    {
        $this->updatedStateId($stateId);
    }

    public function save()
    {
        $this->validate();

        if ($this->shouldPromptForNotifications()) {
            $this->showNotificationConfirm = true;
            return;
        }

        return $this->persistGuest(false);
    }

    public function confirmSaveWithNotifications()
    {
        $this->validate();
        $this->showNotificationConfirm = false;

        return $this->persistGuest(true);
    }

    public function confirmSaveWithoutNotifications()
    {
        $this->validate();
        $this->showNotificationConfirm = false;

        return $this->persistGuest(false);
    }

    public function cancelNotificationPrompt()
    {
        $this->showNotificationConfirm = false;
    }

    private function shouldPromptForNotifications(): bool
    {
        return !empty($this->selectedEvents) && !empty($this->email || $this->phone);
    }

    private function persistGuest(bool $sendNotifications = true)
    {
        // Parse the name into first and last name
        $nameParts = $this->parseName($this->name);

        $guest = Guest::create([
            'customer_id' => $this->customer_id,
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            // 'rsvp_status' => $this->rsvp_status,
            // 'notes' => $this->notes,
        ]);

        // Attach guest to selected events
        if (!empty($this->selectedEvents)) {
            $eventData = [];
            foreach ($this->selectedEvents as $eventId) {
                $eventData[$eventId] = [
                    'attendance_status' => 'invited',
                    'rsvp_token' => Str::random(32),
                    'rsvp_sent_at' => now(),
                    'rsvp_expires_at' => now()->addDays(7),
                ];
            }
            $guest->events()->attach($eventData);

            // Send RSVP invitation via email and SMS/WhatsApp
            if ($sendNotifications && ($guest->email || $guest->phone)) {
                // Load the guest with customer and events relationships
                $guest->loadMissing(['customer', 'events']);

                foreach ($this->selectedEvents as $eventId) {
                    // Reload guest with specific event relationship for this iteration
                    $guestForEvent = Guest::with(['customer', 'events' => function ($query) use ($eventId) {
                        $query->where('events.id', $eventId);
                    }])->find($guest->id);

                    if (!$guestForEvent || $guestForEvent->events->isEmpty()) {
                        continue;
                    }

                    $event = $guestForEvent->events->first();
                    $rsvpToken = $event->pivot->rsvp_token;
                    $rsvpLink = route('rsvp.show', $rsvpToken);
                    $eventName = $event->name ?? 'our event';
                    $eventDate = $event->event_date?->format('F j, Y') ?? 'Date: TBA';
                    $guestName = $guestForEvent->title . ' ' . $guestForEvent->first_name;
                    $customerName = $guestForEvent->customer->full_name ?? 'our customer';
                    $message = "Hi, {$guestName}, you're invited to {$eventName} on {$eventDate}. Please RSVP: {$rsvpLink}";

                    if ($guestForEvent->email) {
                        try {
                            Mail::to($guestForEvent->email)->queue(new GuestRsvpInviteMail($guestForEvent));
                        } catch (\Exception $e) {
                            // Log email sending failure but don't stop the process
                            logger()->error('Failed to send RSVP email to guest: ' . $guestForEvent->email . ' - ' . $e->getMessage());
                        }
                    }

                    if ($guestForEvent->phone) {
                        try {
                            $to = app(TwilioService::class)->formatE164($guestForEvent->phone);
                            if ($to) {
                                $twilioService = app(TwilioService::class);

                                // Try WhatsApp template first, fallback to regular WhatsApp
                                $whatsappSuccess = $twilioService->sendWhatsAppTemplate($to, $guestName, $eventName, $eventDate, $rsvpToken, $customerName);

                                // if (!$whatsappSuccess) {
                                //     $whatsappSuccess = $twilioService->sendWhatsApp($to, $message);
                                // }

                                if ($whatsappSuccess) {
                                    logger()->info('RSVP WhatsApp sent successfully to guest: ' . $to);
                                } else {
                                    logger()->warning('RSVP WhatsApp failed to send to guest: ' . $to);
                                }

                                // Send SMS independently - not as a fallback
                                $smsSuccess = $twilioService->sendSms($to, $message);
                                if ($smsSuccess) {
                                    logger()->info('RSVP SMS sent successfully to guest: ' . $to);
                                } else {
                                    logger()->warning('RSVP SMS failed to send to guest: ' . $to);
                                }
                            } else {
                                logger()->warning('Skipped RSVP notification: unable to format phone for guest: ' . $guestForEvent->phone);
                            }
                        } catch (\Exception $e) {
                            logger()->error('Failed to send RSVP notification to guest: ' . $guestForEvent->phone . ' - ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        $message = 'Guest added successfully';

        if (!empty($this->selectedEvents)) {
            $message .= ' and attached to selected events';
        }

        if ($sendNotifications && ($guest->email || $guest->phone) && !empty($this->selectedEvents)) {
            $message .= '. Message notifications were triggered for the guest.';
        } elseif (!empty($this->selectedEvents)) {
            $message .= '. No message notification was sent.';
        } else {
            $message .= '.';
        }

        session()->flash('message', $message);

        return redirect()->route('guests.list');
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

    #[On('address-updated')]
    public function handleAddressUpdated($field = null, $value = null)
    {
        if ($field === 'address') {
            $this->address = $value;

            if (!empty($value)) {
                $this->autoPopulateStateFromAddress($value);
            }
        }
    }

    #[On('google-places-state-detected')]
    public function handleStateDetected($field = null, $stateName = null)
    {
        if ($field === 'state_id') {
            $this->autoSelectStateByName($stateName);
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

    #[On('coordinates-updated')]
    public function handleCoordinatesUpdated($latitude = null, $longitude = null)
    {
        $this->latitude  = $latitude  !== null ? (string) $latitude  : $this->latitude;
        $this->longitude = $longitude !== null ? (string) $longitude : $this->longitude;
    }

    #[On('address-geocoded')]
    public function handleAddressGeocoded($data)
    {
        // Expect shape: { field: 'address', value: '...', latitude: xx, longitude: yy }
        if (!is_array($data)) {
            return;
        }

        if (($data['field'] ?? null) === 'address') {
            $this->address = $data['value'] ?? $this->address;
            $this->latitude = isset($data['latitude']) ? (string) $data['latitude'] : $this->latitude;
            $this->longitude = isset($data['longitude']) ? (string) $data['longitude'] : $this->longitude;
        }
    }

    #[On('request-server-geocode')]
    public function handleServerGeocode($data)
    {
        // Server-side geocoding fallback. Expects: { address: '...' }
        $address = is_array($data) ? ($data['address'] ?? null) : null;
        $apiKey = config('services.google.places_api_key');
        if (empty($address) || empty($apiKey)) {
            logger()->warning('Server geocode skipped: missing address or API key');
            return;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey,
            ]);

            $json = $response->json();

            if (($json['status'] ?? '') === 'OK' && !empty($json['results'][0]['geometry']['location'])) {
                $loc = $json['results'][0]['geometry']['location'];
                // Emit back to client / other listeners
                $this->emit('address-geocoded', [
                    'field' => 'address',
                    'value' => $address,
                    'latitude' => $loc['lat'],
                    'longitude' => $loc['lng'],
                ]);
            } else {
                logger()->warning('Server geocoding failed: ' . ($json['status'] ?? 'unknown'));
            }
        } catch (\Throwable $e) {
            logger()->error('Server geocode error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.guests.guest-create', [
            'customerEvents' => $this->customerEvents,
            'states' => $this->states,
            'cities' => $this->cities,
        ]);
    }
}
