<?php

namespace App\Mail;

use App\Models\PaymentReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptApprovedMail extends Mailable
{
  use Queueable, SerializesModels;

  public $receipt;
  public $customer;
  public $delivery;

  /**
   * Create a new message instance.
   */
  public function __construct(PaymentReceipt $receipt)
  {
    $this->receipt = $receipt;
    $this->customer = $receipt->customer;

    // Find the associated delivery
    $this->delivery = \App\Models\Delivery::whereHas('event', function ($query) use ($receipt) {
      $query->where('customer_id', $receipt->customer_id);
    })
      ->where('payment_status', 'paid')
      ->where('payment_method', 'offline')
      ->where('delivery_required', true)
      ->first();
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: '✅ Payment Confirmed - Guest Management Access Granted',
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.payment-receipt-approved',
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array
  {
    return [];
  }
}
