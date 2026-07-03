<?php

namespace App\Http\Controllers;

use App\Models\TwilioMessageLog;
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
            $log->update([
                'status' => $status ?? $log->status,
                'error_code' => $errorCode !== null ? (int) $errorCode : $log->error_code,
            ]);
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
}
