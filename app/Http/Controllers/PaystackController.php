<?php

namespace App\Http\Controllers;

use App\Models\PackageCustomization;
use App\Models\PackagePayment;
use App\Models\Delivery;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaystackController extends Controller
{
    public function handle(Request $request)
    {
        $reference = $request->query('reference');

        $response = Http::withToken(config('services.paystack.secret'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}")
            ->json();

        if ($response['status'] && $response['data']['status'] === 'success') {
            $payment = PackagePayment::where('reference', $reference)->firstOrFail();
            $payment->update(['status' => 'success']);

            // Check if this is a delivery service payment
            $items = $payment->items;
            if (isset($items['type']) && $items['type'] === 'delivery_service') {
                // Update delivery payment status
                $delivery = Delivery::find($items['delivery_id']);
                if ($delivery) {
                    $delivery->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }

                // Redirect to delivery services page with post-payment modal
                return redirect()->route('delivery.services')
                    ->with('show_post_payment_modal', true)
                    ->with('message', 'Delivery service payment successful!');
            } else {
                // Handle package customization payment (regular package payments)
                // Update item statuses to ordered
                PackageCustomization::where('customer_id', $payment->customer_id)
                    ->where('status', 'in_cart')
                    ->update(['status' => 'ordered']);

                // Send order confirmation email
                try {
                    $customerEmail = $payment->customer_info['email'] ?? $payment->customer->email;
                    Mail::to($customerEmail)->send(new OrderConfirmationMail($payment, $payment->items));
                } catch (\Exception $e) {
                    Log::error('Failed to send order confirmation email: ' . $e->getMessage());
                }

                // Redirect to order confirmation page
                return redirect()->route('order.confirmation.online', ['reference' => $reference])
                    ->with('success', 'Payment successful! Your order is being processed.');
            }
        }

        // If failed
        if ($payment = PackagePayment::where('reference', $reference)->first()) {
            $payment->update(['status' => 'failed']);

            // Send failure notification email
            try {
                $customerEmail = $payment->customer_info['email'] ?? $payment->customer->email ?? null;
                if ($customerEmail) {
                    // Create a simple failure notification email
                    Mail::raw(
                        "Dear Customer,\n\n" .
                            "Your payment for order {$reference} could not be processed successfully.\n" .
                            "Please try again or contact our support team for assistance.\n\n" .
                            "Best regards,\nThe Oncue Team",
                        function ($message) use ($customerEmail, $reference) {
                            $message->to($customerEmail)
                                ->subject('Payment Failed - Order ' . $reference);
                        }
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to send payment failure email: ' . $e->getMessage());
            }
        }

        return redirect()->route('cart.summary')
            ->with('error', 'Payment verification failed. Please try again or contact support.');
    }

    public function deliveryRedirect(Request $request)
    {
        $deliveryId = $request->query('delivery_id');

        if (!$deliveryId) {
            return redirect()->route('delivery.services')
                ->with('error', 'Invalid delivery request.');
        }

        $delivery = Delivery::find($deliveryId);

        if (!$delivery) {
            return redirect()->route('delivery.services')
                ->with('error', 'Delivery not found.');
        }

        // Get the package payment record
        $packagePayment = PackagePayment::where('items->delivery_id', $deliveryId)
            ->where('status', 'pending')
            ->first();

        if (!$packagePayment) {
            return redirect()->route('delivery.services')
                ->with('error', 'Payment record not found.');
        }

        try {
            // Call Paystack API to initialize transaction
            $response = Http::withToken(config('services.paystack.secret'))
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => Auth::user()->email,
                    'amount' => $packagePayment->amount, // Already in kobo
                    'reference' => $packagePayment->reference,
                    'callback_url' => route('payment.callback'),
                ]);

            $responseData = $response->json();

            if ($response->successful() && $responseData['status']) {
                // Get the authorization URL from Paystack response
                $authorizationUrl = $responseData['data']['authorization_url'];

                Log::info('PaystackController: Successfully initialized transaction', [
                    'delivery_id' => $deliveryId,
                    'reference' => $packagePayment->reference,
                    'authorization_url' => $authorizationUrl
                ]);

                // Redirect to Paystack payment page
                return redirect()->away($authorizationUrl);
            } else {
                Log::error('PaystackController: Failed to initialize transaction', [
                    'delivery_id' => $deliveryId,
                    'reference' => $packagePayment->reference,
                    'response' => $responseData
                ]);

                return redirect()->route('delivery.services')
                    ->with('error', 'Failed to initialize payment: ' . ($responseData['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('PaystackController: Exception during transaction initialization', [
                'delivery_id' => $deliveryId,
                'reference' => $packagePayment->reference,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('delivery.services')
                ->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }
    }
}
