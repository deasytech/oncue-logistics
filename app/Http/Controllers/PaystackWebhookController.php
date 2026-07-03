<?php

namespace App\Http\Controllers;

use App\Models\GuestFabricSelection;
use App\Models\Invoice;
use App\Models\PackagePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    /**
     * Handle Paystack webhook events.
     *
     * Paystack calls this endpoint server-to-server whenever a transaction's status
     * changes, independent of whether the customer's browser ever returns to our
     * callback URL. This is what keeps payment status in sync when a customer closes
     * the tab, loses connection, or pays via a channel (bank transfer/USSD) that
     * confirms asynchronously after they've left the checkout page.
     *
     * Register this URL (https://your-domain/webhooks/paystack) in the Paystack
     * dashboard under Settings > API Keys & Webhooks.
     */
    public function handle(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $secret = config('services.paystack.secret');

        if (!$signature || !$secret || !hash_equals(hash_hmac('sha512', $request->getContent(), $secret), $signature)) {
            Log::warning('Paystack webhook: invalid or missing signature.');
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $payload = $request->json()->all();
        $event = $payload['event'] ?? null;

        if ($event !== 'charge.success') {
            return response()->json(['message' => 'Event ignored']);
        }

        $data = $payload['data'] ?? [];
        $reference = $data['reference'] ?? null;
        $metadata = $data['metadata'] ?? [];

        if (!$reference) {
            Log::warning('Paystack webhook: charge.success with no reference.', ['payload' => $payload]);
            return response()->json(['message' => 'Missing reference'], 400);
        }

        Log::info('Paystack webhook: charge.success received', ['reference' => $reference, 'metadata' => $metadata]);

        // RSVP fabric orders
        if (($metadata['type'] ?? null) === 'rsvp_fabric' || str_starts_with($reference, 'RSVP-')) {
            $fabricSelection = GuestFabricSelection::where('payment_reference', $reference)->first()
                ?? (isset($metadata['order_id']) ? GuestFabricSelection::find($metadata['order_id']) : null);

            if ($fabricSelection) {
                app(PaymentController::class)->confirmFabricSelectionPayment($fabricSelection);
                return response()->json(['message' => 'Fabric order confirmed']);
            }

            Log::warning('Paystack webhook: no GuestFabricSelection found for reference.', ['reference' => $reference]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Invoice payments
        if (isset($metadata['invoice_id'])) {
            $invoice = Invoice::find($metadata['invoice_id']);

            if ($invoice) {
                if ($invoice->payment_status !== 'completed') {
                    $invoice->markAsPaid($data['id'] ?? null);
                }
                return response()->json(['message' => 'Invoice confirmed']);
            }

            Log::warning('Paystack webhook: no Invoice found for metadata.', ['reference' => $reference]);
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        // Package / delivery service payments (cart checkout)
        $payment = PackagePayment::where('reference', $reference)->first();
        if ($payment) {
            app(PaystackController::class)->confirmPackagePayment($payment);
            return response()->json(['message' => 'Package payment confirmed']);
        }

        Log::warning('Paystack webhook: no matching record found for reference.', ['reference' => $reference]);
        return response()->json(['message' => 'No matching record'], 404);
    }
}
