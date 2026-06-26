<?php

namespace App\Mail;

use App\Models\GuestFabricSelection;
use App\Services\DeliveryZoneService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestPaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fabricSelection;

    public function __construct(GuestFabricSelection $fabricSelection)
    {
        $this->fabricSelection = $fabricSelection;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Complete Your Payment - ' . ($this->fabricSelection->event->name ?? 'Your Order'),
        );
    }

    public function content(): Content
    {
        $deliveryZone = null;
        if ($this->fabricSelection->delivery_zone_id) {
            $deliveryZoneService = new DeliveryZoneService();
            $deliveryZone = $deliveryZoneService->getZoneById($this->fabricSelection->delivery_zone_id);
        }

        return new Content(
            view: 'emails.guest-payment-reminder',
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

    public function attachments(): array
    {
        return [];
    }
}
