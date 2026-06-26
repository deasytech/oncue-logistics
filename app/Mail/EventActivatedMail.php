<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Event Has Been Activated – ' . $this->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-activated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
