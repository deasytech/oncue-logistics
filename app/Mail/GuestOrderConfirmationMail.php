<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\GuestFabricSelection;
use App\Services\DeliveryZoneService;

class GuestOrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fabricSelection;

    /**
     * Create a new message instance.
     */
    public function __construct(GuestFabricSelection $fabricSelection)
    {
        $this->fabricSelection = $fabricSelection;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - Fabric Selection',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Get delivery zone information if available
        $deliveryZone = null;
        if ($this->fabricSelection->delivery_zone_id) {
            $deliveryZoneService = new DeliveryZoneService();
            $deliveryZone = $deliveryZoneService->getZoneById($this->fabricSelection->delivery_zone_id);
        }

        return new Content(
            markdown: 'emails.guest-order-confirmation',
            with: [
                'fabricSelection' => $this->fabricSelection,
                'guest' => $this->fabricSelection->guest,
                'event' => $this->fabricSelection->event,
                'deliveryZone' => $deliveryZone,
                'totalAmount' => $this->fabricSelection->calculateTotal(),
                'fabricSelections' => $this->fabricSelection->getFabricSelections(),
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
