<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\City;
use App\Models\State;
use App\Mail\GuestOrderConfirmationMail;
use App\Services\DeliveryZoneService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class RsvpController extends Controller
{
    public function show($token)
    {
        // Find the guest-event relationship by RSVP token
        $guestEvent = DB::table('event_guest')
            ->where('rsvp_token', $token)
            ->first();

        if (!$guestEvent) {
            abort(404, 'Invalid RSVP link.');
        }

        // Check if RSVP link has expired
        if (now()->greaterThan($guestEvent->rsvp_expires_at)) {
            abort(403, 'RSVP link has expired.');
        }

        // Get the guest and event details
        $guest = Guest::findOrFail($guestEvent->guest_id);
        $event = \App\Models\Event::findOrFail($guestEvent->event_id);

        // Get fabric types linked to this event through event_fabric_type pivot table
        $fabricTypes = DB::table('event_fabric_type')
            ->join('fabric_types', 'event_fabric_type.fabric_type_id', '=', 'fabric_types.id')
            ->where('event_fabric_type.event_id', $event->id)
            ->where('fabric_types.is_active', true)
            ->select(
                'fabric_types.id',
                'fabric_types.name',
                'fabric_types.description',
                'event_fabric_type.custom_price as base_price'
            )
            ->get();

        // Delivery zones are now handled in package customizer, not RSVP
        $deliveryZones = [];

        // Check existing fabric selection for this guest
        $existingFabricSelection = \App\Models\GuestFabricSelection::where('guest_id', $guest->id)
            ->where('event_id', $event->id)
            ->first();

        // Get guest's address for delivery
        $deliveryAddress = [
            'address' => $guest->address,
            'city' => $guest->city?->name,
            'state' => $guest->state?->name,
        ];

        $states = State::where('is_active', true)->orderBy('name')->get();
        $selectedStateId = $guest->state_id;
        $selectedCityId = $guest->city_id;
        $cities = $selectedStateId
            ? City::where('state_id', $selectedStateId)->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('pages.rsvp.form', [
            'guest' => $guest,
            'event' => $event,
            'rsvp_data' => $guestEvent,
            'token' => $token,
            'fabricTypes' => $fabricTypes,
            'deliveryZones' => $deliveryZones,
            'existingFabricSelection' => $existingFabricSelection,
            'deliveryAddress' => $deliveryAddress,
            'states' => $states,
            'cities' => $cities,
            'selectedStateId' => $selectedStateId,
            'selectedCityId' => $selectedCityId,
        ]);
    }

    public function submit(Request $request, $token)
    {
        // Server-side fallback: if declined guest didn't submit fabric_purchase_interest, assume "no"
        if ($request->input('attendance_status') === 'declined' && !$request->has('fabric_purchase_interest')) {
            $request->merge(['fabric_purchase_interest' => 'no']);
        }

        // Validation rules
        $rules = [
            'attendance_status' => 'required|in:confirmed,declined',
            'plus_one' => ['nullable', 'string', 'max:100', 'regex:/^[\p{L}\s\'\-]+$/u'],
            'fabric_types' => 'nullable|array',
            'fabric_types.*' => 'exists:fabric_types,id',
            'delivery_zone_id' => 'nullable|integer',
            'payment_method' => 'nullable|in:online',
            'update_address' => 'nullable|boolean',
            'delivery_address' => 'required_if:update_address,1|string|max:500',
            'delivery_state_id' => 'required_if:update_address,1|exists:states,id',
            'delivery_city_id' => [
                'required_if:update_address,1',
                'exists:cities,id',
                Rule::exists('cities', 'id')
                    ->where('state_id', $request->input('delivery_state_id')),
            ],
        ];

        // Add validation for declined guests
        if ($request->input('attendance_status') === 'declined') {
            $rules['fabric_purchase_interest'] = 'required|in:yes,no';

            // If they want to purchase fabric, require fabric selection
            if ($request->input('fabric_purchase_interest') === 'yes') {
                $rules['fabric_types'] = 'required|array|min:1';
                $rules['fabric_types.*'] = 'exists:fabric_types,id';
                $rules['payment_method'] = 'required|in:online';
            }
        }

        $request->validate($rules);

        // Find the guest-event relationship by RSVP token
        $guestEvent = DB::table('event_guest')
            ->where('rsvp_token', $token)
            ->first();

        if (!$guestEvent) {
            abort(404, 'Invalid RSVP link.');
        }

        if (now()->greaterThan($guestEvent->rsvp_expires_at)) {
            abort(403, 'RSVP link has expired.');
        }

        // Get the guest details
        $guest = Guest::findOrFail($guestEvent->guest_id);
        $event = \App\Models\Event::findOrFail($guestEvent->event_id);

        // Update the pivot table with RSVP response
        DB::table('event_guest')
            ->where('rsvp_token', $token)
            ->update([
                'attendance_status' => $request->input('attendance_status'),
                'plus_one' => $request->input('plus_one') ?? null,
                'rsvp_responded_at' => now(),
            ]);

        // Handle fabric selection - store in session for confirmed guests until payment
        $fabricSelection = null;
        if ($request->has('fabric_types') && count($request->fabric_types) > 0) {
            // Get fabrics linked to this event with custom prices from event_fabric_type pivot table
            $eventFabrics = DB::table('event_fabric_type')
                ->join('fabric_types', 'event_fabric_type.fabric_type_id', '=', 'fabric_types.id')
                ->where('event_fabric_type.event_id', $event->id)
                ->whereIn('event_fabric_type.fabric_type_id', $request->fabric_types)
                ->where('fabric_types.is_active', true)
                ->select(
                    'fabric_types.id',
                    'fabric_types.name',
                    'fabric_types.description',
                    'event_fabric_type.custom_price'
                )
                ->get();

            $fabricSelections = [];
            $totalFabricCost = 0;

            foreach ($eventFabrics as $fabric) {
                $fabricSelections[] = [
                    'fabric_id' => $fabric->id,
                    'name' => $fabric->name,
                    'price' => $fabric->custom_price,
                    'quantity' => 1, // Each fabric type selected once
                ];
                $totalFabricCost += $fabric->custom_price;
            }

            // Calculate delivery cost using external API
            $deliveryZoneService = new DeliveryZoneService();
            $deliveryCost = $request->filled('delivery_zone_id')
                ? $deliveryZoneService->getDeliveryCost($request->input('delivery_zone_id'))
                : 0;

            $totalAmount = $totalFabricCost + $deliveryCost;

            // For confirmed guests, create a pending fabric selection and redirect to payment summary
            if ($request->input('attendance_status') === 'confirmed') {
                $fabricSelection = \App\Models\GuestFabricSelection::create([
                    'guest_id' => $guest->id,
                    'event_id' => $event->id,
                    'fabric_selections' => $fabricSelections,
                    'total_fabric_cost' => $totalFabricCost,
                    'delivery_zone_id' => $request->input('delivery_zone_id'),
                    'delivery_cost' => $deliveryCost,
                    'total_amount' => $totalAmount,
                    'payment_method' => $request->input('payment_method', 'online'),
                    'payment_status' => 'pending',
                ]);

                // Send order confirmation email
                if ($guest->email) {
                    Mail::to($guest->email)->send(new GuestOrderConfirmationMail($fabricSelection));
                }

                // Redirect to payment summary
                return redirect()->route('payment.summary', [
                    'token' => $token,
                    'order_id' => $fabricSelection->id
                ])->with('success', 'Please review your order details before proceeding to payment.');
            }

            // For declined guests who want to purchase fabric, save immediately
            // Check if guest already has a fabric selection for this event
            $existingFabricSelection = \App\Models\GuestFabricSelection::where('guest_id', $guest->id)
                ->where('event_id', $event->id)
                ->first();

            if ($existingFabricSelection) {
                // Update existing fabric selection
                $existingFabricSelection->update([
                    'fabric_selections' => $fabricSelections,
                    'total_fabric_cost' => $totalFabricCost,
                    'delivery_zone_id' => $request->input('delivery_zone_id'),
                    'delivery_cost' => $deliveryCost,
                    'total_amount' => $totalAmount,
                    'payment_method' => $request->input('payment_method', 'online'),
                    'payment_status' => 'pending',
                ]);
                $fabricSelection = $existingFabricSelection;
            } else {
                // Create new fabric selection
                $fabricSelection = \App\Models\GuestFabricSelection::create([
                    'guest_id' => $guest->id,
                    'event_id' => $event->id,
                    'fabric_selections' => $fabricSelections,
                    'total_fabric_cost' => $totalFabricCost,
                    'delivery_zone_id' => $request->input('delivery_zone_id'),
                    'delivery_cost' => $deliveryCost,
                    'total_amount' => $totalAmount,
                    'payment_method' => $request->input('payment_method', 'online'),
                    'payment_status' => 'pending',
                ]);
            }

            // Send order confirmation email
            if ($guest->email) {
                Mail::to($guest->email)->send(new GuestOrderConfirmationMail($fabricSelection));
            }
        }

        // Handle package selection and payment if provided
        $packageSelection = null;
        if ($request->filled('package_customization_id')) {
            $packageCustomization = \App\Models\PackageCustomization::findOrFail($request->input('package_customization_id'));

            // Check if guest already has a selection for this event
            $existingSelection = \App\Models\GuestPackageSelection::where('guest_id', $guest->id)
                ->where('event_id', $event->id)
                ->first();

            $deliveryCost = $request->filled('delivery_service_id')
                ? \App\Models\DeliveryService::find($request->input('delivery_service_id'))->cost
                : 0;

            if ($existingSelection) {
                // Update existing selection
                $existingSelection->update([
                    'package_customization_id' => $packageCustomization->id,
                    'quantity' => 1, // Each guest gets 1 quantity
                    'unit_price' => $packageCustomization->unit_price,
                    'total_price' => $packageCustomization->unit_price,
                    'delivery_service_id' => $request->input('delivery_service_id'),
                    'delivery_cost' => $deliveryCost,
                    'payment_method' => $request->input('payment_method', 'online'),
                    'payment_status' => 'pending',
                ]);
                $packageSelection = $existingSelection;
            } else {
                // Create new selection
                $packageSelection = \App\Models\GuestPackageSelection::create([
                    'guest_id' => $guest->id,
                    'event_id' => $event->id,
                    'package_customization_id' => $packageCustomization->id,
                    'quantity' => 1, // Each guest gets 1 quantity
                    'unit_price' => $packageCustomization->unit_price,
                    'total_price' => $packageCustomization->unit_price,
                    'delivery_service_id' => $request->input('delivery_service_id'),
                    'delivery_cost' => $deliveryCost,
                    'payment_method' => $request->input('payment_method', 'online'),
                    'payment_status' => 'pending',
                ]);
            }

            // Send order confirmation email
            if ($guest->email) {
                Mail::to($guest->email)->send(new GuestOrderConfirmationMail($packageSelection));
            }
        }

        // Handle address update if requested (also persist latitude/longitude if provided)
        if ($request->boolean('update_address')) {
            $updateData = [
                'address' => $request->input('delivery_address'),
                'city_id' => $request->input('delivery_city_id'),
                'state_id' => $request->input('delivery_state_id'),
            ];

            // Only update latitude/longitude when provided (allow empty string to clear)
            if ($request->has('delivery_latitude')) {
                $updateData['latitude'] = $request->input('delivery_latitude') === '' ? null : $request->input('delivery_latitude');
            }

            if ($request->has('delivery_longitude')) {
                $updateData['longitude'] = $request->input('delivery_longitude') === '' ? null : $request->input('delivery_longitude');
            }

            $guest->update($updateData);
        } else {
            // If the user did not request a full address update but latitude/longitude were provided
            // persist them so geocoding from the address autocomplete isn't lost.
            $latLngUpdate = [];
            if ($request->has('delivery_latitude')) {
                $latLngUpdate['latitude'] = $request->input('delivery_latitude') === '' ? null : $request->input('delivery_latitude');
            }
            if ($request->has('delivery_longitude')) {
                $latLngUpdate['longitude'] = $request->input('delivery_longitude') === '' ? null : $request->input('delivery_longitude');
            }

            if (!empty($latLngUpdate)) {
                $guest->update($latLngUpdate);
            }
        }

        // Redirect to payment summary if fabric was selected and has a payable amount
        if ($fabricSelection && $fabricSelection->total_amount > 0) {
            return redirect()->route('payment.summary', [
                'token' => $token,
                'order_id' => $fabricSelection->id
            ])->with('success', 'Please review your order details before proceeding to payment.');
        }

        return redirect()->route('rsvp.show', $token)->with('success', 'Your response has been recorded. Thank you!');
    }

    /**
     * Get or create state record
     */
    private function getOrCreateState(string $stateName): int
    {
        $state = \App\Models\State::firstOrCreate(
            ['name' => $stateName],
            ['name' => $stateName]
        );
        return $state->id;
    }

    /**
     * Get or create city record
     */
    private function getOrCreateCity(string $cityName, string $stateName): int
    {
        $stateId = $this->getOrCreateState($stateName);

        $city = \App\Models\City::firstOrCreate(
            ['name' => $cityName, 'state_id' => $stateId],
            ['name' => $cityName, 'state_id' => $stateId]
        );
        return $city->id;
    }

    public function citiesByState(State $state): JsonResponse
    {
        $cities = $state->cities()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($cities);
    }
}
