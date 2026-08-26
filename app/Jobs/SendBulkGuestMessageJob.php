<?php

namespace App\Jobs;

use App\Mail\BulkGuestMessageMail;
use App\Models\BulkMessage;
use App\Models\BulkMessageDelivery;
use App\Models\Guest;
use App\Services\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBulkGuestMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function __construct(
        public int $bulkMessageId,
        public int $guestId,
    ) {
    }

    public function handle(TwilioService $twilio): void
    {
        $bulkMessage = BulkMessage::find($this->bulkMessageId);
        $guest = Guest::find($this->guestId);

        if (!$bulkMessage || !$guest) {
            return;
        }

        foreach ($bulkMessage->channels as $channel) {
            $delivery = BulkMessageDelivery::firstOrCreate(
                [
                    'bulk_message_id' => $bulkMessage->id,
                    'guest_id' => $guest->id,
                    'channel' => $channel,
                ],
                ['status' => 'pending']
            );

            // Retry-safe: a prior attempt for this guest/channel already resolved, skip it.
            if ($delivery->status !== 'pending') {
                continue;
            }

            match ($channel) {
                'email' => $this->sendEmail($delivery, $guest, $bulkMessage),
                'sms' => $this->sendSms($delivery, $guest, $bulkMessage, $twilio),
                'whatsapp' => $this->sendWhatsapp($delivery, $guest, $bulkMessage, $twilio),
                default => $delivery->update(['status' => 'skipped', 'error_message' => "Unknown channel: {$channel}"]),
            };
        }
    }

    protected function personalize(string $text, Guest $guest): string
    {
        return str_ireplace('{{first_name}}', $guest->first_name, $text);
    }

    protected function sendEmail(BulkMessageDelivery $delivery, Guest $guest, BulkMessage $bulkMessage): void
    {
        if (empty($guest->email)) {
            $delivery->update(['status' => 'skipped', 'error_message' => 'Guest has no email on file.']);
            return;
        }

        try {
            Mail::to($guest->email)->queue(new BulkGuestMessageMail(
                $bulkMessage->title,
                $this->personalize($bulkMessage->body, $guest),
            ));

            $delivery->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (Throwable $e) {
            Log::error('Bulk message email failed to queue: ' . $e->getMessage(), ['guest_id' => $guest->id, 'bulk_message_id' => $bulkMessage->id]);
            $delivery->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }

    protected function sendSms(BulkMessageDelivery $delivery, Guest $guest, BulkMessage $bulkMessage, TwilioService $twilio): void
    {
        if (empty($guest->phone)) {
            $delivery->update(['status' => 'skipped', 'error_message' => 'Guest has no phone on file.']);
            return;
        }

        $plainTextBody = $this->htmlToPlainText($this->personalize($bulkMessage->body, $guest));

        $success = $twilio->sendSms($guest->phone, $plainTextBody, 'bulk_message', ['guest_id' => $guest->id]);

        $delivery->update($success
            ? ['status' => 'sent', 'sent_at' => now()]
            : ['status' => 'failed', 'error_message' => 'SMS send failed — see WhatsApp/SMS Logs for details.']);
    }

    protected function sendWhatsapp(BulkMessageDelivery $delivery, Guest $guest, BulkMessage $bulkMessage, TwilioService $twilio): void
    {
        if (empty($guest->phone)) {
            $delivery->update(['status' => 'skipped', 'error_message' => 'Guest has no phone on file.']);
            return;
        }

        $plainTextContent = $this->htmlToPlainText($this->personalize($bulkMessage->body, $guest));

        $outcome = $twilio->sendWhatsAppBulkMessageTemplate($guest->phone, $guest->first_name, $plainTextContent, 'bulk_message', ['guest_id' => $guest->id]);

        $delivery->update($outcome->success
            ? ['status' => 'sent', 'sent_at' => now()]
            : ['status' => 'failed', 'error_message' => $outcome->errorMessage ?? 'WhatsApp send failed — see WhatsApp/SMS Logs for details.']);
    }

    protected function htmlToPlainText(string $html): string
    {
        $withBreaks = preg_replace('/<(br|\/p|\/div|\/h[1-6])\s*\/?>/i', "\n", $html);

        return trim(html_entity_decode(strip_tags($withBreaks), ENT_QUOTES));
    }

    public function failed(Throwable $exception): void
    {
        BulkMessageDelivery::where('bulk_message_id', $this->bulkMessageId)
            ->where('guest_id', $this->guestId)
            ->where('status', 'pending')
            ->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
    }
}
