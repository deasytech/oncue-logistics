<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceNotification extends Mailable
{
  use Queueable, SerializesModels;

  public Invoice $invoice;
  public string $paymentUrl;

  /**
   * Create a new message instance.
   */
  public function __construct(Invoice $invoice)
  {
    $this->invoice = $invoice;
    $this->paymentUrl = route('invoice.payment', [
      'token' => $invoice->payment_token,
    ]);
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: "Invoice #{$this->invoice->invoice_number} from " . config('app.name'),
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.invoice-notification',
      with: [
        'invoice' => $this->invoice,
        'paymentUrl' => $this->paymentUrl,
        'companyName' => config('app.name'),
      ],
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