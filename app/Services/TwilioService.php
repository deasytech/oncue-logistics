<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioService
{
  protected ?Client $client = null;
  protected string $fromNumber;
  protected string $whatsappFromNumber;
  protected string $defaultCountryCode;
  protected ?string $messagingServiceSid;
  protected ?string $senderId;

  public function __construct()
  {
    $sid = config('services.twilio.sid');
    $token = config('services.twilio.token');
    $this->fromNumber = (string) config('services.twilio.from');
    $this->whatsappFromNumber = (string) config('services.twilio.whatsapp_from');
    $this->messagingServiceSid = config('services.twilio.messaging_service_sid');
    $this->senderId = config('services.twilio.sender_id');
    $this->defaultCountryCode = config('services.twilio.default_country_code', '234');

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
    } catch (\Throwable $e) {
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

  public function sendSms(string $to, string $message): bool
  {
    if (!$this->client) {
      Log::warning('Twilio SMS not sent: client not initialized.');
      return false;
    }

    $to = $this->formatE164($to);
    if (!$to) return false;

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
        'body' => $message
      ];

      // Only use messaging service if NO alphanumeric sender ID is configured
      // Alphanumeric sender IDs require direct use of the 'from' parameter
      if (empty($this->senderId) && !empty($this->messagingServiceSid)) {
        $params['messagingServiceSid'] = $this->messagingServiceSid;
        unset($params['from']);
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

      return !empty($response->sid);
    } catch (\Exception $e) {
      Log::error("Twilio SMS error: " . $e->getMessage());
      return false;
    }
  }

  public function sendWhatsApp(string $to, string $message): bool
  {
    if (!$this->client || empty($this->whatsappFromNumber)) {
      Log::warning('Twilio WhatsApp not sent: client not initialized or WhatsApp FROM number missing.');
      return false;
    }

    $to = $this->formatE164($to);
    if (!$to) return false;

    try {
      // WhatsApp numbers need to be prefixed with 'whatsapp:' for Twilio
      $whatsappTo = $to;
      if (!str_starts_with($to, 'whatsapp:')) {
        $whatsappTo = 'whatsapp:' . $to;
      }

      $fromNumber = 'whatsapp:' . $this->whatsappFromNumber;

      Log::info("Sending Twilio WhatsApp", [
        'to' => $whatsappTo,
        'from' => $fromNumber,
      ]);

      $response = $this->client->messages->create($whatsappTo, [
        'from' => $fromNumber,
        'body' => $message
      ]);

      Log::info("Twilio WhatsApp response", [
        'sid' => $response->sid,
        'status' => $response->status,
        'to' => $response->to,
        'from' => $response->from,
      ]);

      return !empty($response->sid);
    } catch (\Exception $e) {
      Log::error("Twilio WhatsApp error: " . $e->getMessage());
      return false;
    }
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
   * @return bool
   */
  public function sendWhatsAppTemplate(string $to, string $guestName, string $eventName, string $eventDate, string $rsvpToken, string $customerName): bool
  {
    if (!$this->client || empty($this->whatsappFromNumber)) {
      Log::warning('Twilio WhatsApp template not sent: client not initialized or WhatsApp FROM number missing.');
      return false;
    }

    $contentSid = config('services.twilio.rsvp_template_sid');
    if (empty($contentSid)) {
      Log::warning('Twilio WhatsApp template not sent: rsvp_template_sid not configured.');
      return false;
    }

    $to = $this->formatE164($to);
    if (!$to) return false;

    try {
      // WhatsApp numbers need to be prefixed with 'whatsapp:' for Twilio
      $whatsappTo = $to;
      if (!str_starts_with($to, 'whatsapp:')) {
        $whatsappTo = 'whatsapp:' . $to;
      }

      $fromNumber = 'whatsapp:' . $this->whatsappFromNumber;

      Log::info("Sending Twilio WhatsApp template", [
        'to' => $whatsappTo,
        'from' => $fromNumber,
        'contentSid' => $contentSid,
      ]);

      $response = $this->client->messages->create(
        $whatsappTo,
        [
          'from' => $fromNumber,
          'contentSid' => $contentSid,
          'contentVariables' => json_encode([
            '1' => $guestName,
            '2' => $eventName,
            '3' => $eventDate,
            '4' => $rsvpToken,
            '5' => $customerName,
          ]),
        ]
      );

      Log::info("Twilio WhatsApp template response", [
        'sid' => $response->sid,
        'status' => $response->status,
        'to' => $response->to,
        'from' => $response->from,
      ]);

      return !empty($response->sid);
    } catch (\Exception $e) {
      Log::error("Twilio WhatsApp template error: " . $e->getMessage());
      return false;
    }
  }
}
