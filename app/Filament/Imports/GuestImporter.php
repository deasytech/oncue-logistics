<?php

namespace App\Filament\Imports;

use App\Mail\GuestRsvpInviteMail;
use App\Models\Event;
use App\Models\Guest;
use App\Services\TwilioService;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GuestImporter extends Importer
{
    protected static ?string $model = Guest::class;

    public static function getOptionsFormComponents(): array
    {
        return [];
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:45']),
            ImportColumn::make('first_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('last_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->rules(['email', 'max:255']),
            ImportColumn::make('phone')
                ->rules(['max:255']),
            ImportColumn::make('address')
                ->rules(['max:500']),
            ImportColumn::make('city_id')
                ->numeric()
                ->rules(['exists:cities,id']),
            ImportColumn::make('state_id')
                ->numeric()
                ->rules(['exists:states,id']),
            ImportColumn::make('notes')
                ->rules(['max:1000']),
        ];
    }

    public function resolveRecord(): ?Guest
    {
        // Get customer_id from options
        $customerId = $this->options['customer_id'] ?? null;

        if (!$customerId) {
            return null; // Don't import if no customer is selected
        }

        // Set customer_id in data for the import
        $this->data['customer_id'] = $customerId;

        // Find guest by email and customer_id to avoid duplicates across customers
        return Guest::firstOrNew([
            'email' => $this->data['email'] ?? null,
            'customer_id' => $customerId,
        ]);
    }

    public static function shouldSkipField(string $field): bool
    {
        // Don't skip any fields - let all imported data be processed
        return false;
    }

    protected function beforeCreate(): void
    {
        // event_id is handled in afterSave through pivot table
        // rsvp_token will be set when attaching to event
    }

    protected function afterSave(): void
    {
        $guest = $this->record;
        $eventId = $this->options['event_id'] ?? null;
        $sendNotifications = (bool) ($this->options['send_notifications'] ?? true);

        if ($eventId) {
            // Attach guest to event with RSVP details
            $rsvpToken = Str::random(32);
            $guest->events()->attach($eventId, [
                'rsvp_token' => $rsvpToken,
                'rsvp_sent_at' => now(),
                'rsvp_expires_at' => now()->addDays(7),
                'attendance_status' => 'invited',
            ]);

            if ($sendNotifications && ($guest->email || $guest->phone)) {
                // Reload the guest with the event relationship
                $guest->loadMissing(['customer', 'events' => function ($query) use ($eventId) {
                    $query->where('events.id', $eventId);
                }]);

                $event = $guest->events->first();
                $rsvpLink = route('rsvp.show', $rsvpToken);
                $eventDate = $event->event_date?->format('F j, Y') ?? 'Date: TBA';
                $eventName = $event->name ?? 'our event';
                $guestName = $guest->full_name;
                $customerName = $guest->customer->full_name ?? 'our customer';
                $message = "Hi {$guestName}, you're invited to {$eventName} on {$eventDate}. Confirm: {$rsvpLink}";

                if ($guest->email) {
                    logger()->info('Sending RSVP email to guest: ' . $guest->email . ' for event: ' . $eventId);
                    logger()->info('CUSTOMER NAME: ' . $customerName);

                    try {
                        Mail::to($guest->email)->queue(new GuestRsvpInviteMail($guest));
                        logger()->info('RSVP email queued successfully for guest: ' . $guest->email);
                    } catch (\Exception $e) {
                        logger()->error('Failed to queue RSVP email for guest: ' . $guest->email . ' - ' . $e->getMessage());
                        // Don't throw the exception to allow the import to continue
                    }
                }

                if ($guest->phone) {
                    try {
                        $to = app(TwilioService::class)->formatE164($guest->phone);
                        if ($to) {
                            $twilioService = app(TwilioService::class);

                            // Try WhatsApp template first, fallback to regular WhatsApp
                            $whatsappSuccess = $twilioService->sendWhatsAppTemplate($to, $guestName, $eventName, $eventDate, $rsvpToken, $customerName);

                            // if (!$whatsappSuccess) {
                            //     $whatsappSuccess = $twilioService->sendWhatsApp($to, $message);
                            // }

                            if ($whatsappSuccess) {
                                logger()->info('RSVP WhatsApp sent successfully to guest: ' . $to);
                            } else {
                                logger()->warning('RSVP WhatsApp failed to send to guest: ' . $to);
                            }

                            // Send SMS independently - not as a fallback
                            $smsSuccess = $twilioService->sendSms($to, $message);
                            if ($smsSuccess) {
                                logger()->info('RSVP SMS sent successfully to guest: ' . $to);
                            } else {
                                logger()->warning('RSVP SMS failed to send to guest: ' . $to);
                            }
                        } else {
                            logger()->warning('Skipped RSVP notification: unable to format phone for guest: ' . $guest->phone);
                        }
                    } catch (\Exception $e) {
                        logger()->error('Failed to send RSVP notification to guest: ' . $guest->phone . ' - ' . $e->getMessage());
                    }
                }
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $eventName = Event::find($import->options['event_id'] ?? null)?->name;
        $sendNotifications = (bool) ($import->options['send_notifications'] ?? true);

        $body = "Your guest import for event '{$eventName}' has completed. "
            . number_format($import->successful_rows)
            . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount)
                . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        if (($import->options['event_id'] ?? null) && !$sendNotifications) {
            $body .= ' Email and SMS notifications were not sent.';
        }

        return $body;
    }
}
