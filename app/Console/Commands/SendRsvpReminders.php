<?php

namespace App\Console\Commands;

use App\Models\Guest;
use App\Services\TwilioService;
use Illuminate\Console\Command;

class SendRsvpReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rsvp:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send RSVP reminders via WhatsApp and SMS to unconfirmed guests';

    /**
     * Execute the console command.
     */
    public function handle(TwilioService $twilio)
    {
        // Get guests with pending RSVPs through the event_guest pivot table
        $guests = Guest::whereHas('events', function ($query) {
            $query->whereNull('event_guest.rsvp_responded_at')
                ->where('event_guest.rsvp_sent_at', '<=', now()->subDays(7))
                ->where(function ($q) {
                    $q->whereNull('event_guest.rsvp_expires_at')
                        ->orWhere('event_guest.rsvp_expires_at', '>', now());
                })
                ->where('event_guest.reminder_attempts', '<', 3);
        })->with(['events' => function ($query) {
            $query->whereNull('event_guest.rsvp_responded_at')
                ->where('event_guest.rsvp_sent_at', '<=', now()->subDays(7))
                ->where(function ($q) {
                    $q->whereNull('event_guest.rsvp_expires_at')
                        ->orWhere('event_guest.rsvp_expires_at', '>', now());
                })
                ->where('event_guest.reminder_attempts', '<', 3);
        }])->get();

        foreach ($guests as $guest) {
            // Get the first event that needs RSVP reminder
            $event = $guest->events->first();
            if (!$event) continue;

            $pivot = $event->pivot;
            $eventDate = $event->event_date?->format('F j, Y') ?? 'Date: TBA';
            $eventName = $event->name;
            $guestName = $guest->full_name;
            $rsvpToken = $pivot->rsvp_token;
            $customerName = $event->customer->full_name;
            $rsvpLink = route('rsvp.show', $rsvpToken);
            $message = "Hi {$guestName}, just a reminder to RSVP to {$eventName} on {$eventDate}. Tap here: " . $rsvpLink;

            $whatsappSuccess = $twilio->sendWhatsAppTemplate($guest->phone, $guestName, $eventName, $eventDate, $rsvpToken, $customerName);

            $sent = false;
            if ($whatsappSuccess) {
                $sent = true;
                $this->info("WhatsApp reminder sent to {$guest->phone} for event {$event->name}");
            } else {
                // WhatsApp failed, try SMS as fallback
                $smsSuccess = $twilio->sendSms($guest->phone, $message);
                if ($smsSuccess) {
                    $sent = true;
                    $this->info("WhatsApp failed, SMS fallback sent to {$guest->phone} for event {$event->name}");
                } else {
                    $this->error("Failed to send to {$guest->phone} for event {$event->name} via WhatsApp and SMS");
                }
            }

            // Update attempts and last sent timestamp in pivot table if at least one channel succeeded
            if ($sent) {
                $guest->events()->updateExistingPivot($event->id, [
                    'reminder_attempts' => $pivot->reminder_attempts + 1,
                    'last_reminder_sent_at' => now(),
                ]);
            }

            // Throttle: wait 1 second to avoid API rate limits
            sleep(1);
        }

        return 0;
    }
}
