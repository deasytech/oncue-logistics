<?php

namespace App\Jobs;

use App\Models\GuestFabricSelection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendGuestOrderToDeliveryMiddleware implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(public GuestFabricSelection $fabricSelection)
    {
    }

    /**
     * Backoff (seconds) between retry attempts: 1m, 5m, 15m, 1h.
     * Only takes effect with a real queue worker — under QUEUE_CONNECTION=sync
     * (this project's default) the job runs once, inline, with no queue-level retry.
     */
    public function backoff(): array
    {
        return [60, 300, 900, 3600];
    }

    public function handle(): void
    {
        $fabricSelection = $this->fabricSelection;

        // Already delivered by a previous attempt (or a concurrent dispatch) — nothing to do.
        if ($fabricSelection->external_order_id) {
            return;
        }

        $guest = $fabricSelection->guest;

        if (!$fabricSelection->delivery_zone_id) {
            Log::error('Guest fabric selection has no delivery_zone_id — order sent to delivery middleware without a zone', [
                'fabric_selection_id' => $fabricSelection->id,
                'guest_id' => $fabricSelection->guest_id,
                'event_id' => $fabricSelection->event_id,
            ]);
        }

        // Prepare order data according to the expected format
        $orderData = [
            'external_order_id' => 'RSVP-' . $fabricSelection->id . '-' . time(),
            'zone_id' => $fabricSelection->delivery_zone_id,

            'customer_name' => $guest->full_name,
            'customer_email' => $guest->email,
            'customer_phone' => $guest->phone,

            'street_address' => $guest->address ?? 'No address provided',
            'city' => $guest->city?->name ?? 'Lagos',
            'state' => $guest->state?->name ?? 'Lagos',
            'latitude' => $guest->latitude ?? null,
            'longitude' => $guest->longitude ?? null,

            'order_amount' => (int) $fabricSelection->total_amount, // Amount is already in kobo format

            'items' => [],
        ];

        // Add fabric items to the order
        foreach ($fabricSelection->fabric_selections as $fabric) {
            $orderData['items'][] = [
                'name' => $fabric['name'],
                'quantity' => $fabric['quantity'] ?? 1,
                'price' => (int) $fabric['price'], // Price is already in the correct format (kobo)
            ];
        }

        // Note: Delivery service is NOT added as a separate item to the order items array
        // as requested. The delivery cost is already included in the total_amount field.

        $apiUrl = config('delivery.middleware_url') . config('delivery.endpoints.orders');
        $apiKey = config('delivery.middleware_api_key');

        // A handful of fast in-process retries absorb transient network blips
        // regardless of queue driver; job-level $tries/backoff (above) handle
        // longer outages when a real queue worker is running.
        $response = Http::timeout(config('delivery.timeout'))
            ->retry(3, 2000)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-KEY' => $apiKey,
            ])
            ->post($apiUrl, $orderData);

        $contentType = $response->header('Content-Type');

        Log::info('Delivery middleware API response', [
            'fabric_selection_id' => $fabricSelection->id,
            'request_url' => $apiUrl,
            'method' => 'POST',
            'attempt' => $this->attempts(),
            'status' => $response->status(),
            'successful' => $response->successful(),
            'content_type' => $contentType,
            'body' => $response->body(),
        ]);

        // Ensure the response is JSON before decoding
        if (! str_contains($contentType ?? '', 'application/json')) {
            throw new \RuntimeException(
                "Delivery middleware returned a non-JSON response (status {$response->status()}) for fabric selection {$fabricSelection->id}."
            );
        }

        $responseData = $response->json();

        if ($response->successful() && isset($responseData['order_id'])) {
            $fabricSelection->update([
                'external_order_id' => $responseData['order_id'],
            ]);

            Log::info('Order successfully sent to delivery middleware', [
                'fabric_selection_id' => $fabricSelection->id,
                'external_order_id' => $responseData['order_id'],
            ]);

            return;
        }

        // Throwing (instead of logging and swallowing) lets the queue's retry/backoff
        // mechanism actually kick in on unexpected responses, not just exceptions.
        throw new \RuntimeException(
            "Delivery middleware returned an unexpected response (status {$response->status()}) for fabric selection {$fabricSelection->id}: "
            . json_encode($responseData)
        );
    }

    /**
     * Called once all retry attempts are exhausted, so the failure is never
     * just a single swallowed log line — this is the durable record that the
     * order never reached delivery and needs manual follow-up.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Order permanently failed to reach the delivery middleware after all retries', [
            'fabric_selection_id' => $this->fabricSelection->id,
            'guest_id' => $this->fabricSelection->guest_id,
            'event_id' => $this->fabricSelection->event_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
