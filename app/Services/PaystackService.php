<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
  protected string $secretKey;
  protected string $baseUrl = 'https://api.paystack.co';

  public function __construct()
  {
    $this->secretKey = config('services.paystack.secret');
  }

  /**
   * Initialize a payment transaction
   */
  public function initializePayment(Invoice $invoice, string $callbackUrl): array
  {
    try {
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->secretKey,
        'Content-Type' => 'application/json',
      ])
        ->timeout(30)
        ->retry(3, 1000)
        ->post("{$this->baseUrl}/transaction/initialize", [
          'email' => $invoice->customer_email,
          'amount' => $invoice->balance_due * 100, // Paystack expects amount in kobo
          'reference' => $this->generateReference($invoice),
          'callback_url' => $callbackUrl,
          'metadata' => [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'customer_name' => $invoice->customer_name,
            'payment_token' => $invoice->payment_token,
          ],
        ]);

      if ($response->successful()) {
        $data = $response->json('data');

        // Update invoice with Paystack reference
        $invoice->update([
          'paystack_reference' => $data['reference'],
          'payment_status' => 'processing',
        ]);

        return [
          'success' => true,
          'authorization_url' => $data['authorization_url'],
          'access_code' => $data['access_code'],
          'reference' => $data['reference'],
        ];
      }

      Log::error('Paystack initialization failed', [
        'invoice_id' => $invoice->id,
        'response' => $response->json(),
      ]);

      return [
        'success' => false,
        'message' => $response->json('message', 'Failed to initialize payment'),
      ];
    } catch (\Exception $e) {
      Log::error('Paystack initialization error', [
        'invoice_id' => $invoice->id,
        'error' => $e->getMessage(),
      ]);

      return [
        'success' => false,
        'message' => 'An error occurred while initializing payment. Please check your internet connection and try again.',
      ];
    }
  }

  /**
   * Verify a payment transaction
   */
  public function verifyPayment(string $reference): array
  {
    try {
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->secretKey,
      ])
        ->timeout(30)
        ->retry(3, 1000)
        ->get("{$this->baseUrl}/transaction/verify/{$reference}");

      if ($response->successful()) {
        $data = $response->json('data');

        return [
          'success' => true,
          'status' => $data['status'],
          'amount' => $data['amount'] / 100, // Convert from kobo to naira
          'reference' => $data['reference'],
          'transaction_id' => $data['id'],
          'paid_at' => $data['paid_at'],
          'channel' => $data['channel'],
          'metadata' => $data['metadata'] ?? [],
        ];
      }

      Log::error('Paystack verification failed', [
        'reference' => $reference,
        'response' => $response->json(),
      ]);

      return [
        'success' => false,
        'message' => $response->json('message', 'Failed to verify payment'),
      ];
    } catch (\Exception $e) {
      Log::error('Paystack verification error', [
        'reference' => $reference,
        'error' => $e->getMessage(),
      ]);

      return [
        'success' => false,
        'message' => 'Unable to verify payment due to network timeout. The payment may still be processing. Please wait a moment and refresh the page, or contact support if the issue persists.',
      ];
    }
  }

  /**
   * Generate a unique payment reference
   */
  protected function generateReference(Invoice $invoice): string
  {
    return 'INV_' . $invoice->invoice_number . '_' . time();
  }

  /**
   * Handle Paystack webhook
   */
  public function handleWebhook(array $payload): array
  {
    // Verify webhook signature
    $signature = request()->header('x-paystack-signature');
    $computedHash = hash_hmac('sha512', json_encode($payload), $this->secretKey);

    if (!hash_equals($computedHash, $signature)) {
      Log::warning('Invalid Paystack webhook signature');
      return ['success' => false, 'message' => 'Invalid signature'];
    }

    $event = $payload['event'] ?? null;

    if ($event === 'charge.success') {
      $data = $payload['data'];
      $reference = $data['reference'];
      $metadata = $data['metadata'] ?? [];

      $invoiceId = $metadata['invoice_id'] ?? null;

      if ($invoiceId) {
        $invoice = Invoice::find($invoiceId);

        if ($invoice) {
          $invoice->markAsPaid($data['id']);
          return ['success' => true, 'message' => 'Payment processed successfully'];
        }
      }
    }

    return ['success' => true, 'message' => 'Event processed'];
  }
}
