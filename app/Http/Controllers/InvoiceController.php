<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
  protected PaystackService $paystackService;

  public function __construct(PaystackService $paystackService)
  {
    $this->paystackService = $paystackService;
  }

  /**
   * Show invoice payment page
   */
  public function showPayment(Request $request, string $token)
  {
    $invoice = Invoice::where('payment_token', $token)
      ->where('token_expires_at', '>', now())
      ->firstOrFail();

    if ($invoice->is_paid) {
      return view('invoices.paid', compact('invoice'));
    }

    return view('invoices.payment', compact('invoice'));
  }

  /**
   * Initialize Paystack payment
   */
  public function initializePayment(Request $request, string $token)
  {
    $invoice = Invoice::where('payment_token', $token)
      ->where('token_expires_at', '>', now())
      ->firstOrFail();

    if ($invoice->is_paid) {
      return redirect()->route('invoice.paid', ['token' => $token])
        ->with('info', 'This invoice has already been paid.');
    }

    $callbackUrl = route('invoice.payment.callback', ['token' => $token]);

    $result = $this->paystackService->initializePayment($invoice, $callbackUrl);

    if ($result['success']) {
      return redirect()->away($result['authorization_url']);
    }

    return back()->with('error', $result['message'] ?? 'Failed to initialize payment. Please try again.');
  }

  /**
   * Handle Paystack callback
   */
  public function handleCallback(Request $request, string $token)
  {
    $reference = $request->get('reference');

    if (!$reference) {
      return redirect()->route('invoice.payment', ['token' => $token])
        ->with('error', 'Invalid payment reference.');
    }

    $invoice = Invoice::where('payment_token', $token)->firstOrFail();

    // Check if already paid
    if ($invoice->is_paid) {
      return redirect()->route('invoice.paid', ['token' => $token])
        ->with('success', 'Payment has already been confirmed!');
    }

    $result = $this->paystackService->verifyPayment($reference);

    if (!$result['success']) {
      // Check if it's a timeout/network error (payment might still be processing)
      if (str_contains($result['message'] ?? '', 'timeout') || str_contains($result['message'] ?? '', 'network')) {
        // Store reference for later verification
        if (!$invoice->paystack_reference) {
          $invoice->update(['paystack_reference' => $reference]);
        }

        return redirect()->route('invoice.payment', ['token' => $token])
          ->with('warning', $result['message'] . ' You can try refreshing this page in a few moments to check payment status.');
      }

      return redirect()->route('invoice.payment', ['token' => $token])
        ->with('error', $result['message'] ?? 'Payment verification failed.');
    }

    if ($result['status'] === 'success') {
      $invoice->markAsPaid($result['transaction_id']);

      return redirect()->route('invoice.paid', ['token' => $token])
        ->with('success', 'Payment successful! Thank you for your payment.');
    }

    return redirect()->route('invoice.payment', ['token' => $token])
      ->with('error', 'Payment was not successful. Please try again.');
  }

  /**
   * Retry payment verification
   */
  public function retryVerification(Request $request, string $token)
  {
    $invoice = Invoice::where('payment_token', $token)->firstOrFail();

    if ($invoice->is_paid) {
      return redirect()->route('invoice.paid', ['token' => $token])
        ->with('success', 'Payment has already been confirmed!');
    }

    $reference = $invoice->paystack_reference;

    if (!$reference) {
      return redirect()->route('invoice.payment', ['token' => $token])
        ->with('error', 'No payment reference found.');
    }

    $result = $this->paystackService->verifyPayment($reference);

    if ($result['success'] && $result['status'] === 'success') {
      $invoice->markAsPaid($result['transaction_id']);

      return redirect()->route('invoice.paid', ['token' => $token])
        ->with('success', 'Payment verified successfully! Thank you for your payment.');
    }

    return redirect()->route('invoice.payment', ['token' => $token])
      ->with('info', 'Payment verification pending. Please try again in a few moments.');
  }

  /**
   * Show paid confirmation page
   */
  public function showPaid(Request $request, string $token)
  {
    $invoice = Invoice::where('payment_token', $token)->firstOrFail();

    return view('invoices.paid', compact('invoice'));
  }
}
