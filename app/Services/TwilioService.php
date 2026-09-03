<?php

namespace App\Services;

use App\Models\TwilioMessageLog;
use App\Services\Twilio\SendOutcome;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Throwable;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class TwilioService
{
  protected ?Client $client = null;
  protected string $fromNumber;
  protected string $whatsappFromNumber;
  protected string $defaultCountryCode;
  protected ?string $messagingServiceSid;
  protected ?string $senderId;
  protected int $whatsappMps;

  public function __construct()
  {
    $sid = config('services.twilio.sid');
    $token = config('services.twilio.token');
    $this->fromNumber = (string) config('services.twilio.from');
    $this->whatsappFromNumber = (string) config('services.twilio.whatsapp_from');
    $this->messagingServiceSid = config('services.twilio.messaging_service_sid');
    $this->senderId = config('services.twilio.sender_id');
    $this->defaultCountryCode = config('services.twilio.default_country_code', '234');
    // WhatsApp's default per-sender throughput cap is 80 msg/sec; we default far below that
    // since this app's send volume never needs to approach the ceiling.
    $this->whatsappMps = max(1, (int) config('services.twilio.whatsapp_mps', 5));

    // Normalize and validate WhatsApp FROM number (should be E.164 without the "whatsapp:" prefix)
    if (!empty($this->whatsappFromNumber)) {
      $normalized = preg_replace('/^whatsapp:/i', '', $this->whatsappFromNumber);
      $normalized = preg_replace('/[^\d+]/', '', $normalized);
      if (!str_starts_with($normalized, '+') && str_starts_with($normalized, $this->defaultCountryCode)) {
        $normalized = '+' . $normalized;
      }
      if (preg_match('/^\+\d{7,15}$/', $normalized)) {
        $this->whatsappFromNumber = $normalized;
      } else {
        Log::warning('Configured Twilio WhatsApp FROM number is invalid; WhatsApp messages will be skipped.', ['configured' => $this->whatsappFromNumber]);
        $this->whatsappFromNumber = '';
      }
    }

    if (empty($sid) || empty($token)) {
      Log::warning('Twilio is not configured: missing SID/token.');
      return;
    }

    if (!class_exists(Client::class)) {
      Log::error('Twilio SDK is not installed. Run: composer require twilio/sdk');
      return;
    }

    try {
      $this->client = new Client($sid, $token);
    } catch (Throwable $e) {
      Log::error('Failed to initialize Twilio client: ' . $e->getMessage());
      $this->client = null;
    }
  }

  /**
   * Normalize a phone number to E.164. Assumes national numbers missing a '+'
   * should use the configured default country code.
   */
  public function formatE164(?string $phone): ?string
  {
    if (!$phone) {
      return null;
    }

    // Remove everything except digits and +
    $phone = trim($phone);
    $phone = preg_replace('/[^\d+]/', '', $phone);

    if (empty($phone)) {
      return null;
    }

    // Already valid E.164
    if (str_starts_with($phone, '+')) {
      return $phone;
    }

    // International format beginning with 00
    if (str_starts_with($phone, '00')) {
      return '+' . substr($phone, 2);
    }

    // Nigerian local number: 09078337079
    if (preg_match('/^0\d{10}$/', $phone)) {
      return '+' . $this->defaultCountryCode . substr($phone, 1);
    }

    // Nigerian number without leading zero: 9078337079
    if (preg_match('/^\d{10}$/', $phone)) {
      return '+' . $this->defaultCountryCode . $phone;
    }

    // Already contains country code but missing +
    if (str_starts_with($phone, $this->defaultCountryCode)) {
      return '+' . $phone;
    }

    // Fallback
    return '+' . ltrim($phone, '+');
  }

  /**
   * Strict E.164 check: '+' followed by 7-15 digits, first digit 1-9.
   * Use this to gate sends — formatE164() normalizes but does not validate.
   */
  public function isValidE164(?string $phone): bool
  {
    return is_string($phone) && preg_match('/^\+[1-9]\d{6,14}$/', $phone) === 1;
  }

  public function sendSms(string $to, string $message, string $context = 'ad_hoc', array $meta = []): bool
  {
    if (!$this->client) {
      Log::warning('Twilio SMS not sent: client not initialized.');
      return false;
    }

    $formatted = $this->formatE164($to);
    if (!$formatted || !$this->isValidE164($formatted)) {
      Log::warning('Twilio SMS not sent: invalid phone number.', ['to' => $to]);
      $this->logAttempt('sms', $to, null, 'failed', null, 'Invalid phone number format', $context, $meta, 21211);
      return false;
    }
    $to = $formatted;

    // Determine the from address - use sender ID (alphanumeric) if available
    // Alphanumeric sender IDs like "OnCue" must be used without messaging service
    $from = $this->senderId ?? $this->fromNumber;

    if (empty($from)) {
      Log::warning('Twilio SMS not sent: no sender ID or FROM number configured.');
      return false;
    }

    try {
      $params = [
        'from' => $from,
        'body' => $message,
      ];

      // Only use messaging service if NO alphanumeric sender ID is configured
      // Alphanumeric sender IDs require direct use of the 'from' parameter
      if (empty($this->senderId) && !empty($this->messagingServiceSid)) {
        $params['messagingServiceSid'] = $this->messagingServiceSid;
        unset($params['from']);
      }

      if ($callbackUrl = $this->statusCallbackUrl()) {
        $params['statusCallback'] = $callbackUrl;
      }

      Log::info("Sending Twilio SMS", [
        'to' => $to,
        'from' => $params['from'] ?? 'using_messaging_service',
        'using_sender_id' => !empty($this->senderId),
      ]);

      $response = $this->client->messages->create($to, $params);

      Log::info("Twilio SMS sent successfully", [
        'sid' => $response->sid,
        'status' => $response->status,
      ]);

      $this->logAttempt('sms', $to, null, $response->status ?: 'queued', $response->sid, null, $context, $meta, null, ['message' => $message]);

      return !empty($response->sid);
    } catch (RestException $e) {
      Log::error("Twilio SMS error: " . $e->getMessage(), ['error_code' => $e->getCode(), 'to' => $to]);
      $this->logAttempt('sms', $to, null, 'failed', null, $e->getMessage(), $context, $meta, $e->getCode(), ['message' => $message]);
      return false;
    } catch (\Exception $e) {
      Log::error("Twilio SMS error: " . $e->getMessage());
      $this->logAttempt('sms', $to, null, 'failed', null, $e->getMessage(), $context, $meta, null, ['message' => $message]);
      return false;
    }
  }

  /**
   * Send a free-form WhatsApp message. Only usable within an open 24h session
   * window (i.e. in reply to a user message) — Meta will reject this outside
   * that window, so template sends should be preferred for outbound-initiated
   * messages. Kept for completeness; not currently used by any caller.
   */
  public function sendWhatsApp(string $to, string $message, string $context = 'ad_hoc', array $meta = []): SendOutcome
  {
    if (!$this->client || empty($this->whatsappFromNumber)) {
      Log::warning('Twilio WhatsApp not sent: client not initialized or WhatsApp FROM number missing.');
      return SendOutcome::failed(null, 'Client not initialized or WhatsApp FROM missing.');
    }

    $formatted = $this->formatE164($to);
    if (!$formatted || !$this->isValidE164($formatted)) {
      Log::warning('Twilio WhatsApp not sent: invalid phone number.', ['to' => $to]);
      $this->logAttempt('whatsapp', $to, null, 'failed', null, 'Invalid phone number format', $context, $meta, 21211);
      return SendOutcome::failed(21211, 'Invalid phone number format', fallbackToSmsRecommended: false);
    }

    return $this->dispatchWhatsApp($formatted, ['body' => $message], null, 'whatsapp', $context, $meta, payload: ['message' => $message]);
  }

  /**
   * Send WhatsApp message using a Twilio Content Template
   *
   * @param string $to The recipient phone number
   * @param string $guestName Guest name for variable 1
   * @param string $eventName Event name for variable 2
   * @param string $eventDate Event date for variable 3
   * @param string $rsvpToken RSVP token for variable 4 (just the token, not full URL)
   * @param string $customerName Customer name for variable 5
   * @param string $context Label for what triggered this send (e.g. rsvp_invite, rsvp_reminder) — stored for metrics
   */
  public function sendWhatsAppTemplate(
    string $to,
    string $guestName,
    string $eventName,
    string $eventDate,
    string $rsvpToken,
    string $customerName,
    string $context = 'rsvp_invite',
    array $meta = []
  ): SendOutcome {
    if (!$this->client || empty($this->whatsappFromNumber)) {
      Log::warning('Twilio WhatsApp template not sent: client not initialized or WhatsApp FROM number missing.');
      return SendOutcome::failed(null, 'Client not initialized or WhatsApp FROM missing.');
    }

    $contentSid = config('services.twilio.rsvp_template_sid');
    if (empty($contentSid)) {
      Log::warning('Twilio WhatsApp template not sent: rsvp_template_sid not configured.');
      return SendOutcome::failed(null, 'rsvp_template_sid not configured.');
    }

    $formatted = $this->formatE164($to);
    if (!$formatted || !$this->isValidE164($formatted)) {
      Log::warning('Twilio WhatsApp template not sent: invalid phone number.', ['to' => $to]);
      $this->logAttempt('whatsapp_template', $to, $contentSid, 'failed', null, 'Invalid phone number format', $context, $meta, 21211);
      return SendOutcome::failed(21211, 'Invalid phone number format', fallbackToSmsRecommended: false);
    }

    $params = [
      'contentSid' => $contentSid,
      'contentVariables' => json_encode([
        '1' => $guestName,
        '2' => $eventName,
        '3' => $eventDate,
        '4' => $rsvpToken,
        '5' => $customerName,
      ]),
    ];

    $payload = [
      'guest_name' => $guestName,
      'event_name' => $eventName,
      'event_date' => $eventDate,
      'rsvp_token' => $rsvpToken,
      'customer_name' => $customerName,
    ];

    return $this->dispatchWhatsApp($formatted, $params, $contentSid, 'whatsapp_template', $context, $meta, payload: $payload);
  }

  /**
   * Send a WhatsApp message using the bulk-message Twilio Content Template
   * (a separate template from the RSVP one, since it carries different copy).
   *
   * @param string $to The recipient phone number
   * @param string $firstName Guest first name for variable 1
   * @param string $content Message content for variable 2
   * @param string $context Label for what triggered this send (e.g. bulk_message) — stored for metrics
   */
  public function sendWhatsAppBulkMessageTemplate(
    string $to,
    string $firstName,
    string $content,
    string $context = 'bulk_message',
    array $meta = []
  ): SendOutcome {
    if (!$this->client || empty($this->whatsappFromNumber)) {
      Log::warning('Twilio WhatsApp bulk template not sent: client not initialized or WhatsApp FROM number missing.');
      return SendOutcome::failed(null, 'Client not initialized or WhatsApp FROM missing.');
    }

    $contentSid = config('services.twilio.bulk_message_template_sid');
    if (empty($contentSid)) {
      Log::warning('Twilio WhatsApp bulk template not sent: bulk_message_template_sid not configured.');
      return SendOutcome::failed(null, 'bulk_message_template_sid not configured.');
    }

    $formatted = $this->formatE164($to);
    if (!$formatted || !$this->isValidE164($formatted)) {
      Log::warning('Twilio WhatsApp bulk template not sent: invalid phone number.', ['to' => $to]);
      $this->logAttempt('whatsapp_template', $to, $contentSid, 'failed', null, 'Invalid phone number format', $context, $meta, 21211);
      return SendOutcome::failed(21211, 'Invalid phone number format', fallbackToSmsRecommended: false);
    }

    $params = [
      'contentSid' => $contentSid,
      'contentVariables' => json_encode([
        '1' => $firstName,
        '2' => $content,
      ]),
    ];

    $payload = [
      'first_name' => $firstName,
      'content' => $content,
    ];

    return $this->dispatchWhatsApp($formatted, $params, $contentSid, 'whatsapp_template', $context, $meta, payload: $payload);
  }

  /**
   * Shared send path for both free-form and template WhatsApp messages:
   * throttles to stay under WhatsApp's per-sender throughput limit, retries
   * once on rate-limit errors, and classifies the remaining Twilio error
   * codes so callers know whether an SMS fallback is worth attempting.
   *
   * $payload carries enough of the original call to replay it verbatim from a
   * "Retry" action in the admin UI (template variables or free-form body).
   */
  protected function dispatchWhatsApp(string $to, array $params, ?string $contentSid, string $channel, string $context, array $meta, int $attempt = 1, array $payload = []): SendOutcome
  {
    $this->throttleWhatsAppSend();

    $whatsappTo = 'whatsapp:' . $to;
    $fromNumber = 'whatsapp:' . $this->whatsappFromNumber;
    $params['from'] = $fromNumber;
    if ($callbackUrl = $this->statusCallbackUrl()) {
      $params['statusCallback'] = $callbackUrl;
    }

    try {
      Log::info("Sending Twilio {$channel}", ['to' => $whatsappTo, 'from' => $fromNumber, 'contentSid' => $contentSid, 'attempt' => $attempt]);

      $response = $this->client->messages->create($whatsappTo, $params);

      Log::info("Twilio {$channel} response", [
        'sid' => $response->sid,
        'status' => $response->status,
        'to' => $response->to,
        'from' => $response->from,
      ]);

      $this->logAttempt($channel, $to, $contentSid, $response->status ?: 'queued', $response->sid, null, $context, $meta, null, $payload);

      return SendOutcome::ok($response->sid);
    } catch (RestException $e) {
      $code = $e->getCode();
      $callingCode = $this->guessCallingCode($to);

      Log::error("Twilio {$channel} error", [
        'to' => $to,
        'to_calling_code' => $callingCode,
        'content_sid' => $contentSid,
        'error_code' => $code,
        'error_message' => $e->getMessage(),
        'attempt' => $attempt,
      ]);

      // 63018: sending faster than WhatsApp's throughput limit. Back off briefly and
      // retry once — our own throttle should normally prevent this, so a second hit
      // usually means another process/worker is sending concurrently.
      if ($code === 63018 && $attempt === 1) {
        usleep(500_000);
        return $this->dispatchWhatsApp($to, $params, $contentSid, $channel, $context, $meta, $attempt + 1, $payload);
      }

      // 63049: Meta is blocking delivery because the template is categorized as
      // "Marketing" — US recipients cannot receive Marketing-category WhatsApp
      // templates at all (Meta policy since April 2025). Retrying will never help;
      // this needs to be fixed by re-categorizing the content SID in the Twilio
      // Content Editor if the content is actually transactional/utility.
      if ($code === 63049 && $callingCode === '1') {
        Log::warning(
          'Twilio 63049 for a US recipient: WhatsApp template is blocked because it is categorized as Marketing. ' .
            'If this content is transactional (order/RSVP updates, reminders, OTPs), re-categorize it as Utility/Authentication in the Twilio Content Editor.',
          ['content_sid' => $contentSid, 'to' => $to]
        );
      }

      $fallbackToSmsRecommended = $this->isSmsFallbackRecommendedForError($code);

      $this->logAttempt($channel, $to, $contentSid, 'failed', null, $e->getMessage(), $context, $meta, $code, $payload);

      return SendOutcome::failed($code, $e->getMessage(), $fallbackToSmsRecommended);
    } catch (\Exception $e) {
      Log::error("Twilio {$channel} error: " . $e->getMessage());
      $this->logAttempt($channel, $to, $contentSid, 'failed', null, $e->getMessage(), $context, $meta, null, $payload);
      return SendOutcome::failed(null, $e->getMessage());
    }
  }

  /**
   * Enforce a shared, cross-process floor between WhatsApp sends so bulk
   * imports and on-demand sends can't collectively exceed WhatsApp's
   * per-sender throughput limit (default 80 msg/sec). Uses a cache lock to
   * atomically reserve the next send slot, then sleeps outside the lock so
   * other processes can reserve their own slots while this one waits.
   */
  protected function throttleWhatsAppSend(): void
  {
    $minIntervalMicros = (int) (1_000_000 / $this->whatsappMps);
    $slotKey = 'twilio:whatsapp:throttle:next_slot';
    $waitMicros = 0;

    try {
      Cache::lock('twilio:whatsapp:throttle:lock', 5)->block(5, function () use ($slotKey, $minIntervalMicros, &$waitMicros) {
        $now = (int) (microtime(true) * 1_000_000);
        $nextSlot = (int) Cache::get($slotKey, $now);
        $slot = max($now, $nextSlot);
        Cache::put($slotKey, $slot + $minIntervalMicros, now()->addSeconds(5));
        $waitMicros = $slot - $now;
      });
    } catch (Throwable $e) {
      // If the cache backend can't provide locks, fail open rather than blocking sends.
      Log::warning('Twilio WhatsApp throttle lock unavailable, sending without cross-process throttling: ' . $e->getMessage());
      return;
    }

    if ($waitMicros > 0) {
      usleep($waitMicros);
    }
  }

  /**
   * Whether a WhatsApp failure with the given Twilio error code is worth
   * retrying over SMS. 21211 (malformed 'To') and 63024 (invalid/unreachable
   * WhatsApp recipient) are terminal for this number — an SMS to the same
   * number would fail the same way, so don't burn an attempt on it.
   *
   * Shared by the synchronous failure path in dispatchWhatsApp() and by
   * TwilioStatusWebhookController, which applies the same rule to failures
   * that only become known later via Twilio's async delivery-status callback
   * (e.g. 63049/63021, where the initial API call succeeds and the rejection
   * arrives after the caller has already moved on).
   */
  public function isSmsFallbackRecommendedForError(?int $errorCode): bool
  {
    return !in_array($errorCode, [21211, 63024], true);
  }

  /**
   * Best-effort calling code extraction for logging/metrics (e.g. flagging
   * US recipients for the 63049 Marketing-category block). Not a full
   * country-code lookup — just enough to distinguish common cases.
   */
  protected function guessCallingCode(string $e164): string
  {
    $digits = ltrim($e164, '+');

    return match (true) {
      str_starts_with($digits, '1') => '1',
      str_starts_with($digits, '234') => '234',
      str_starts_with($digits, '44') => '44',
      str_starts_with($digits, '233') => '233',
      default => substr($digits, 0, 3),
    };
  }

  protected function statusCallbackUrl(): ?string
  {
    if (!config('services.twilio.status_callback_enabled', true)) {
      return null;
    }

    try {
      return URL::route('webhooks.twilio.status');
    } catch (Throwable $e) {
      return null;
    }
  }

  protected function logAttempt(
    string $channel,
    string $to,
    ?string $contentSid,
    string $status,
    ?string $messageSid,
    ?string $errorMessage,
    string $context,
    array $meta,
    ?int $errorCode = null,
    array $payload = []
  ): void {
    try {
      TwilioMessageLog::create([
        'channel' => $channel,
        'to' => $to,
        'to_country' => $this->guessCallingCode($this->formatE164($to) ?? $to),
        'context' => $context,
        'content_sid' => $contentSid,
        'message_sid' => $messageSid,
        'status' => $status,
        'error_code' => $errorCode,
        'error_message' => $errorMessage,
        'payload' => $payload ?: null,
        'retry_of_id' => $meta['retry_of_id'] ?? null,
        'guest_id' => $meta['guest_id'] ?? null,
        'event_id' => $meta['event_id'] ?? null,
      ]);

      if (!empty($meta['retry_of_id'])) {
        TwilioMessageLog::whereKey($meta['retry_of_id'])->update(['retried_at' => now()]);
      }
    } catch (Throwable $e) {
      // Never let metrics logging break a send.
      Log::warning('Failed to persist TwilioMessageLog: ' . $e->getMessage());
    }
  }
}
