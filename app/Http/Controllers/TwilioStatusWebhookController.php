<?php

namespace App\Http\Controllers;

use App\Models\TwilioMessageLog;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Security\RequestValidator;

class TwilioStatusWebhookController extends Controller
{
    /**
     * Twilio calls this server-to-server every time a message's delivery status
     * changes (queued -> sent -> delivered/read/failed/undelivered), independent
     * of whether anyone is watching the Twilio console. This is what lets error
     * codes (63049, 63018, 21211, etc.) get tracked as they happen instead of
     * being discovered later by manually pulling logs.
     *
     * Register this URL (https://your-domain/webhooks/twilio/status) as the
     * Messaging Service's "Status Callback URL" in the Twilio console, or it is
     * set per-message automatically by TwilioService when
     * TWILIO_STATUS_CALLBACK_ENABLED=true.
     */
    public function handle(Request $request)
    {
        $token = config('services.twilio.token');
        $signature = $request->header('X-Twilio-Signature');

        if (!$token || !$signature || !(new RequestValidator($token))->validate(
            $signature,
            $request->fullUrl(),
            $request->all()
        )) {
            Log::warning('Twilio status webhook: invalid or missing signature.');
            return response('Invalid signature', 403);
        }

        $messageSid = $request->input('MessageSid') ?? $request->input('SmsSid');
        $status = $request->input('MessageStatus') ?? $request->input('SmsStatus');
        $errorCode = $request->input('ErrorCode');

        if (!$messageSid) {
            return response('Missing MessageSid', 400);
        }

        $log = TwilioMessageLog::where('message_sid', $messageSid)->latest('id')->first();

        if ($log) {
            $wasAlreadyTerminal = in_array($log->status, ['failed', 'undelivered'], true);

            $log->update([
                'status' => $status ?? $log->status,
                'error_code' => $errorCode !== null ? (int) $errorCode : $log->error_code,
            ]);

            // The initial synchronous send can succeed (status "queued") and only fail
            // later once Meta actually evaluates delivery (e.g. 63049/63021) — by then
            // the caller that sent it has already moved on treating it as a success, so
            // none of the normal SMS-fallback logic ever runs. Do it here instead, once,
            // the first time a row transitions into a terminal failure.
            if (!$wasAlreadyTerminal && in_array($log->status, ['failed', 'undelivered'], true)) {
                $this->attemptRsvpSmsFallback($log);
            }
        } else {
            // Status arrived before/without a matching outbound log row (e.g. callback
            // configured at the Messaging Service level rather than per-message).
            TwilioMessageLog::create([
                'channel' => str_starts_with((string) $request->input('To'), 'whatsapp:') ? 'whatsapp' : 'sms',
                'to' => $request->input('To') ?? 'unknown',
                'to_country' => null,
                'context' => 'status_callback',
                'content_sid' => null,
                'message_sid' => $messageSid,
                'status' => $status ?? 'unknown',
                'error_code' => $errorCode !== null ? (int) $errorCode : null,
                'error_message' => null,
            ]);
        }

        if ($errorCode) {
            Log::warning('Twilio delivery error via status callback', [
                'message_sid' => $messageSid,
                'status' => $status,
                'error_code' => $errorCode,
                'to' => $request->input('To'),
            ]);
        }

        return response('OK', 200);
    }

    /**
     * Send the SMS fallback for an RSVP WhatsApp template message that only
     * failed once Meta evaluated delivery, mirroring the fallback every
     * synchronous caller (GuestCreate, SendRsvpReminders, etc.) already
     * performs right after a send-time failure. Scoped to the RSVP invite/
     * reminder template specifically — bulk messaging tracks delivery through
     * a separate model with its own semantics and isn't affected.
     */
    private function attemptRsvpSmsFallback(TwilioMessageLog $log): void
    {
        if ($log->channel !== 'whatsapp_template' || !in_array($log->context, ['rsvp_invite', 'rsvp_reminder'], true)) {
            return;
        }

        if ($log->content_sid !== config('services.twilio.rsvp_template_sid')) {
            return;
        }

        if (!app(TwilioService::class)->isSmsFallbackRecommendedForError($log->error_code)) {
            return;
        }

        $payload = $log->payload ?? [];
        $guestName = $payload['guest_name'] ?? null;
        $eventName = $payload['event_name'] ?? null;
        $eventDate = $payload['event_date'] ?? null;
        $rsvpToken = $payload['rsvp_token'] ?? null;

        if (!$log->to || !$guestName || !$eventName || !$eventDate || !$rsvpToken) {
            Log::warning('Twilio status webhook: cannot send RSVP SMS fallback, payload incomplete.', ['log_id' => $log->id]);
            return;
        }

        $rsvpLink = route('rsvp.show', $rsvpToken);
        $message = $log->context === 'rsvp_reminder'
            ? "Hi {$guestName}, just a reminder to RSVP to {$eventName} on {$eventDate}. Tap here: {$rsvpLink}"
            : "Hi {$guestName}, you're invited to {$eventName} on {$eventDate}. Please RSVP: {$rsvpLink}";

        Log::warning('Twilio status webhook: RSVP WhatsApp failed after being accepted, falling back to SMS.', [
            'log_id' => $log->id,
            'to' => $log->to,
            'error_code' => $log->error_code,
        ]);

        app(TwilioService::class)->sendSms($log->to, $message, $log->context, [
            'guest_id' => $log->guest_id,
            'event_id' => $log->event_id,
            'retry_of_id' => $log->id,
        ]);
    }
}
