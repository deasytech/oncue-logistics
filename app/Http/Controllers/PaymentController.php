<?php

namespace App\Http\Controllers;

use App\Models\GuestFabricSelection;
use App\Mail\GuestOrderConfirmationMail;
use App\Mail\GuestPaymentReminderMail;
use App\Services\DeliveryZoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Show payment preview page (before saving to database)
     */
    public function preview($token)
    {
        $pendingData = session('pending_fabric_selection');

        // Verify the pending data belongs to this token
        if (!$pendingData || $pendingData['token'] !== $token) {
            return redirect()->route('rsvp.show', $token)
                ->with('error', 'No pending order found. Please submit your RSVP again.');
        }

        $guest = \App\Models\Guest::findOrFail($pendingData['guest_id']);
        $event = \App\Models\Event::findOrFail($pendingData['event_id']);

        // Get delivery zone information if available
        $deliveryZone = null;
        if ($pendingData['delivery_zone_id']) {
            $deliveryZoneService = new DeliveryZoneService();
            $deliveryZone = $deliveryZoneService->getZoneById($pendingData['delivery_zone_id']);
        }

        return view('pages.payment.preview', [
            'pendingData' => $pendingData,
            'token' => $token,
            'guest' => $guest,
            'event' => $event,
            'totalAmount' => $pendingData['total_amount'],
            'eventFabrics' => $pendingData['fabric_selections'],
            'deliveryZone' => $deliveryZone,
        ]);
    }

    /**
     * Create the fabric selection and redirect to payment summary
     */
    public function confirmAndProceed(Request $request, $token)
    {
        $pendingData = session('pending_fabric_selection');

        if (!$pendingData || $pendingData['token'] !== $token) {
            return redirect()->route('rsvp.show', $token)
                ->with('error', 'No pending order found. Please submit your RSVP again.');
        }

        $guest = \App\Models\Guest::findOrFail($pendingData['guest_id']);
        $event = \App\Models\Event::findOrFail($pendingData['event_id']);

        // Check if guest already has a fabric selection for this event
        $existingFabricSelection = GuestFabricSelection::where('guest_id', $guest->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existingFabricSelection) {
            // Update existing fabric selection
            $existingFabricSelection->update([
                'fabric_selections' => $pendingData['fabric_selections'],
                'total_fabric_cost' => $pendingData['total_fabric_cost'],
                'delivery_zone_id' => $pendingData['delivery_zone_id'],
                'delivery_cost' => $pendingData['delivery_cost'],
                'total_amount' => $pendingData['total_amount'],
                'payment_method' => $pendingData['payment_method'],
                'payment_status' => 'pending',
            ]);
            $fabricSelection = $existingFabricSelection;
        } else {
            // Create new fabric selection
            $fabricSelection = GuestFabricSelection::create([
                'guest_id' => $guest->id,
                'event_id' => $event->id,
                'fabric_selections' => $pendingData['fabric_selections'],
                'total_fabric_cost' => $pendingData['total_fabric_cost'],
                'delivery_zone_id' => $pendingData['delivery_zone_id'],
                'delivery_cost' => $pendingData['delivery_cost'],
                'total_amount' => $pendingData['total_amount'],
                'payment_method' => $pendingData['payment_method'],
                'payment_status' => 'pending',
            ]);
        }

        // Clear the pending data from session
        session()->forget('pending_fabric_selection');

        // Send order confirmation email
        if ($guest->email) {
            Mail::to($guest->email)->send(new GuestOrderConfirmationMail($fabricSelection));
        }

        // Redirect to payment summary
        return redirect()->route('payment.summary', [
            'token' => $token,
            'order_id' => $fabricSelection->id
        ]);
    }

    /**
     * Show payment summary page
     */
    public function summary($token, $order_id)
    {
        $fabricSelection = GuestFabricSelection::with(['guest.city', 'guest.state'])
            ->findOrFail($order_id);

        // Verify the order belongs to the guest with this token
        $guestEvent = DB::table('event_guest')
            ->where('rsvp_token', $token)
            ->where('guest_id', $fabricSelection->guest_id)
            ->first();

        if (!$guestEvent) {
            abort(404, 'Invalid order or token.');
        }

        // Check if payment is already completed
        if ($fabricSelection->payment_status === 'paid') {
            return redirect()->route('rsvp.show', $token)
                ->with('success', 'Payment has already been completed for this order.');
        }

        // Get delivery zone information if available
        $deliveryZone = null;
        if ($fabricSelection->delivery_zone_id) {
            $deliveryZoneService = new DeliveryZoneService();
            $deliveryZone = $deliveryZoneService->getZoneById($fabricSelection->delivery_zone_id);
        }

        return view('pages.payment.summary', [
            'fabricSelection' => $fabricSelection,
            'token' => $token,
            'totalAmount' => $fabricSelection->calculateTotal(),
            'eventFabrics' => $fabricSelection->getFabricSelections(), // Pass fabric details for display
            'deliveryZone' => $deliveryZone,
        ]);
    }

    /**
     * Handle summary form submission: update guest email if empty, then proceed
     */
    public function submitSummary(Request $request, $token, $order_id)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $fabricSelection = GuestFabricSelection::with('guest')->findOrFail($order_id);

        // Verify the order belongs to the guest with this token
        $guestEvent = DB::table('event_guest')
            ->where('rsvp_token', $token)
            ->where('guest_id', $fabricSelection->guest_id)
            ->first();

        if (!$guestEvent) {
            abort(404, 'Invalid order or token.');
        }

        // Update guest email if currently empty
        if (empty($fabricSelection->guest->email)) {
            $fabricSelection->guest->update(['email' => $request->email]);
        }

        // Route based on payment method
        if ($fabricSelection->payment_method === 'online') {
            return $this->initializePaystack($token, $order_id);
        }

        return redirect()->route('payment.offline', ['token' => $token, 'order_id' => $order_id]);
    }

    /**
     * Show offline payment instructions
     */
    public function showOfflineInstructions($token, $order_id)
    {
        $fabricSelection = GuestFabricSelection::with('guest')->findOrFail($order_id);

        // Verify the order belongs to the guest with this token
        $guestEvent = DB::table('event_guest')
            ->where('rsvp_token', $token)
            ->where('guest_id', $fabricSelection->guest_id)
            ->first();

        if (!$guestEvent) {
            abort(404, 'Invalid order or token.');
        }

        return view('pages.payment.offline-confirmation', [
            'fabricSelection' => $fabricSelection,
            'token' => $token,
        ]);
    }

    /**
     * Initialize Paystack payment
     */
    public function initializePaystack($token, $order_id)
    {
        $fabricSelection = GuestFabricSelection::findOrFail($order_id);

        // Verify the order belongs to the guest with this token
        $guestEvent = DB::table('event_guest')
            ->where('rsvp_token', $token)
            ->where('guest_id', $fabricSelection->guest_id)
            ->first();

        if (!$guestEvent) {
            abort(404, 'Invalid order or token.');
        }

        // Check if payment is already completed
        if ($fabricSelection->payment_status === 'paid') {
            return redirect()->route('rsvp.show', $token)
                ->with('success', 'Payment has already been completed for this order.');
        }

        try {
            Log::info('Initializing Paystack payment', [
                'order_id' => $order_id,
                'token' => $token,
                'guest_email' => $fabricSelection->guest->email,
                'amount' => $fabricSelection->calculateTotal() * 100,
                'paystack_secret' => substr(config('services.paystack.secret') ?? '', 0, 10) . '...'
            ]);

            // Call Paystack API to initialize transaction
            $response = Http::withToken(config('services.paystack.secret'))
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $fabricSelection->guest->email,
                    'amount' => $fabricSelection->calculateTotal() * 100, // Convert to kobo
                    'reference' => 'RSVP-' . strtoupper(uniqid()),
                    'callback_url' => route('payment.paystack.callback', ['token' => $token, 'order_id' => $order_id]),
                    'metadata' => [
                        'order_id' => $order_id,
                        'guest_id' => $fabricSelection->guest_id,
                        'token' => $token,
                        'type' => 'rsvp_fabric',
                    ],
                ]);

            $responseData = $response->json();

            Log::info('Paystack API response', [
                'status' => $response->status(),
                'response_data' => $responseData,
                'order_id' => $order_id
            ]);

            if ($response->successful() && $responseData['status']) {
                // Store the reference in the fabric selection
                $fabricSelection->update([
                    'payment_reference' => $responseData['data']['reference'],
                ]);

                // Redirect to Paystack payment page
                return redirect()->away($responseData['data']['authorization_url']);
            } else {
                Log::error('Paystack payment initialization failed', [
                    'order_id' => $order_id,
                    'response_status' => $response->status(),
                    'response_data' => $responseData
                ]);

                return redirect()->route('payment.summary', ['token' => $token, 'order_id' => $order_id])
                    ->with('error', 'Failed to initialize payment: ' . ($responseData['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization exception', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('payment.summary', ['token' => $token, 'order_id' => $order_id])
                ->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle Paystack callback
     */
    public function handlePaystackCallback($token, $order_id, Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('payment.summary', ['token' => $token, 'order_id' => $order_id])
                ->with('error', 'Invalid payment callback.');
        }

        $fabricSelection = GuestFabricSelection::findOrFail($order_id);

        // Verify the transaction with Paystack
        $response = Http::withToken(config('services.paystack.secret'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}")
            ->json();

        if ($response['status'] && $response['data']['status'] === 'success') {
            // Update payment status
            $fabricSelection->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Now that payment is confirmed, mark the guest's RSVP as confirmed
            DB::table('event_guest')
                ->where('rsvp_token', $token)
                ->where('guest_id', $fabricSelection->guest_id)
                ->update([
                    'attendance_status' => 'confirmed',
                    'rsvp_responded_at' => now(),
                ]);

            // Send order to delivery middleware after successful payment
            $this->sendOrderToDeliveryMiddleware($fabricSelection);

            // Send payment confirmation email
            if ($fabricSelection->guest->email) {
                Mail::to($fabricSelection->guest->email)->send(new GuestOrderConfirmationMail($fabricSelection));
            }

            return redirect()->route('rsvp.show', $token)
                ->with('success', 'Payment completed successfully! Your order has been confirmed.');
        } else {
            $paystackTransactionStatus = $response['data']['status'] ?? 'failed';

            // 'abandoned' means the guest closed the payment window — keep order as pending so they can retry
            $newStatus = ($paystackTransactionStatus === 'abandoned') ? 'pending' : 'failed';
            $fabricSelection->update(['payment_status' => $newStatus]);

            // Send payment reminder email so the guest can complete their payment
            if ($fabricSelection->guest->email) {
                Mail::to($fabricSelection->guest->email)->send(new GuestPaymentReminderMail($fabricSelection));
            }

            return redirect()->route('payment.summary', ['token' => $token, 'order_id' => $order_id])
                ->with('error', 'Payment was not completed. Please try again — a reminder has been sent to your email.');
        }
    }

    /**
     * Show payment processing page (kept for backup/demo)
     */
    public function process($token, $order_id)
    {
        $fabricSelection = GuestFabricSelection::findOrFail($order_id);

        // Verify the order belongs to the guest with this token
        $guestEvent = DB::table('event_guest')
            ->where('rsvp_token', $token)
            ->where('guest_id', $fabricSelection->guest_id)
            ->first();

        if (!$guestEvent) {
            abort(404, 'Invalid order or token.');
        }

        // Check if payment is already completed
        if ($fabricSelection->payment_status === 'paid') {
            return redirect()->route('rsvp.show', $token)
                ->with('success', 'Payment has already been completed for this order.');
        }

        return view('pages.payment.process', [
            'fabricSelection' => $fabricSelection,
            'token' => $token,
            'totalAmount' => $fabricSelection->calculateTotal(),
        ]);
    }

    /**
     * Handle payment completion (simulated for demo)
     */
    public function complete($token, $order_id, Request $request)
    {
        $fabricSelection = GuestFabricSelection::findOrFail($order_id);

        // Verify the order belongs to the guest with this token
        $guestEvent = DB::table('event_guest')
            ->where('rsvp_token', $token)
            ->where('guest_id', $fabricSelection->guest_id)
            ->first();

        if (!$guestEvent) {
            abort(404, 'Invalid order or token.');
        }

        // Simulate payment processing
        // In a real implementation, this would integrate with a payment gateway
        $paymentSuccess = $this->simulatePaymentProcessing($request);

        if ($paymentSuccess) {
            // Update payment status
            $fabricSelection->update([
                'payment_status' => 'paid',
                'payment_reference' => 'PAY-' . strtoupper(uniqid()),
                'paid_at' => now(),
            ]);

            // Send payment confirmation email
            if ($fabricSelection->guest->email) {
                Mail::to($fabricSelection->guest->email)->send(new GuestOrderConfirmationMail($fabricSelection));
            }

            return redirect()->route('rsvp.show', $token)
                ->with('success', 'Payment completed successfully! Your order has been confirmed.');
        } else {
            $fabricSelection->update(['payment_status' => 'failed']);

            return redirect()->route('payment.process', ['token' => $token, 'order_id' => $order_id])
                ->with('error', 'Payment failed. Please try again.');
        }
    }


    /**
     * Send order to delivery middleware after successful payment
     */
    private function sendOrderToDeliveryMiddleware($fabricSelection)
    {
        try {
            $guest = $fabricSelection->guest;
            $event = $fabricSelection->event;

            // Prepare order data according to the expected format
            $orderData = [
                'external_order_id' => 'RSVP-' . $fabricSelection->id . '-' . time(),
                'zone_id' => $fabricSelection->delivery_zone_id ?? 2, // Default to zone 2 if not specified

                'customer_name' => $guest->full_name,
                'customer_email' => $guest->email,
                'customer_phone' => $guest->phone,

                'street_address' => $guest->address ?? 'No address provided',
                'city' => $guest->city?->name ?? 'Lagos',
                'state' => $guest->state?->name ?? 'Lagos',
                'latitude' => $guest->latitude ?? null,
                'longitude' => $guest->longitude ?? null,

                'order_amount' => (int) $fabricSelection->total_amount, // Amount is already in kobo format

                'items' => []
            ];

            // Add fabric items to the order
            foreach ($fabricSelection->fabric_selections as $fabric) {
                $orderData['items'][] = [
                    'name' => $fabric['name'],
                    'quantity' => $fabric['quantity'] ?? 1,
                    'price' => (int) $fabric['price'] // Price is already in the correct format (kobo)
                ];
            }

            // Note: Delivery service is NOT added as a separate item to the order items array
            // as requested. The delivery cost is already included in the total_amount field.

            // Send the order to the external endpoint
            $apiUrl = config('delivery.middleware_url') . config('delivery.endpoints.orders');
            $apiKey = config('delivery.middleware_api_key');

            $response = Http::timeout(config('delivery.timeout'))
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-KEY' => $apiKey
                ])
                ->post($apiUrl, $orderData);

            // Log the response for debugging
            Log::info('Delivery middleware API response', [
                'order_id' => $fabricSelection->id,
                'status' => $response->status(),
                'response' => $response->body(),
                'request_data' => $orderData
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Store the external order ID if returned
                if (isset($responseData['order_id'])) {
                    $fabricSelection->update([
                        'external_order_id' => $responseData['order_id']
                    ]);
                }

                Log::info('Order successfully sent to delivery middleware', [
                    'fabric_selection_id' => $fabricSelection->id,
                    'external_order_id' => $responseData['order_id'] ?? null
                ]);
            } else {
                Log::error('Failed to send order to delivery middleware', [
                    'fabric_selection_id' => $fabricSelection->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'request_data' => $orderData
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while sending order to delivery middleware', [
                'fabric_selection_id' => $fabricSelection->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Simulate payment processing (for demo purposes)
     */
    private function simulatePaymentProcessing(Request $request)
    {
        // In a real implementation, this would integrate with Paystack, Flutterwave, etc.
        // For demo, we'll simulate a 90% success rate
        return rand(1, 100) <= 90;
    }
}
