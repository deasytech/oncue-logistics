<?php

namespace App\Filament\Resources\GuestResource\Pages;

use App\Filament\Resources\GuestResource;
use App\Mail\GuestRsvpInviteMail;
use App\Services\TwilioService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateGuest extends CreateRecord
{
    protected static string $resource = GuestResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Guest registered')
            ->body('The guest has been created successfully.');
    }

    protected function afterCreate(): void
    {
        $guest = $this->record;

        // Check if guest has email and is attached to at least one event
        if ($guest->email && $guest->events->isNotEmpty()) {
            try {
                Mail::to($guest->email)->send(new GuestRsvpInviteMail($guest));

                // Show success notification for email sent
                Notification::make()
                    ->success()
                    ->title('RSVP Email Sent')
                    ->body('An RSVP invitation has been sent to ' . $guest->email)
                    ->send();
            } catch (\Exception $e) {
                // Show error notification if email fails
                Notification::make()
                    ->warning()
                    ->title('Email Failed')
                    ->body('Failed to send RSVP email: ' . $e->getMessage())
                    ->send();
            }
        }

        // Send WhatsApp/SMS invitation if guest has phone number and is attached to events
        if ($guest->phone && $guest->events->isNotEmpty()) {
            try {
                $event = $guest->events->first();
                $rsvpToken = $event->pivot->rsvp_token;
                $rsvpLink = route('rsvp.show', $rsvpToken);
                $eventName = $event->name ?? 'our event';
                $eventDate = $event->event_date?->format('F j, Y') ?? 'Date: TBA';
                $guestName = $guest->full_name;
                $customerName = $guest->customer->full_name ?? 'our customer';
                $message = "Hi {$guestName}, you're invited to {$eventName} on {$eventDate}. Please RSVP: {$rsvpLink}";

                $to = app(TwilioService::class)->formatE164($guest->phone);
                if ($to) {
                    $twilioService = app(TwilioService::class);

                    // Try WhatsApp template first, fallback to regular WhatsApp
                    $whatsappSuccess = $twilioService->sendWhatsAppTemplate($to, $guestName, $eventName, $eventDate, $rsvpToken, $customerName);

                    // if (!$whatsappSuccess) {
                    //     $whatsappSuccess = $twilioService->sendWhatsApp($to, $message);
                    // }

                    if ($whatsappSuccess) {
                        Notification::make()
                            ->success()
                            ->title('RSVP WhatsApp Sent')
                            ->body('An RSVP WhatsApp invitation has been sent to ' . $guest->phone)
                            ->send();
                    } else {
                        // WhatsApp failed, try SMS as fallback
                        $smsSuccess = $twilioService->sendSms($to, $message);
                        if ($smsSuccess) {
                            Notification::make()
                                ->success()
                                ->title('RSVP SMS Sent')
                                ->body('WhatsApp failed. An RSVP SMS invitation has been sent to ' . $guest->phone)
                                ->send();
                        } else {
                            Notification::make()
                                ->warning()
                                ->title('Notification Failed')
                                ->body('Failed to send RSVP via WhatsApp and SMS to ' . $guest->phone)
                                ->send();
                        }
                    }
                }
            } catch (\Exception $e) {
                // Show error notification if WhatsApp/SMS fails
                Notification::make()
                    ->warning()
                    ->title('WhatsApp/SMS Failed')
                    ->body('Failed to send RSVP WhatsApp/SMS: ' . $e->getMessage())
                    ->send();
            }
        }
    }
}
