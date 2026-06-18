<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;
use Vonage\Messages\Channel\WhatsApp\WhatsAppText;

class VonageService
{
  protected Client $client;

  public function __construct()
  {
    $credentials = new Basic(config('services.vonage.key'), config('services.vonage.secret'));
    $this->client = new Client($credentials);
  }

  public function sendSms(string $to, string $message): bool
  {
    try {
      $response = $this->client->sms()->send(new SMS($to, config('services.vonage.from'), $message));
      $status = $response->current()->getStatus();

      return $status == 0;
    } catch (\Exception $e) {
      Log::error("Vonage SMS error: " . $e->getMessage());
      return false;
    }
  }

  public function sendWhatsApp(string $to, string $message): bool
  {
    try {
      $textMessage = new WhatsAppText(
        to: $to,
        from: config('services.vonage.whatsapp_from'),
        text: $message
      );

      $response = $this->client->messages()->send($textMessage);

      return !empty($response['message_uuid']);
    } catch (\Exception $e) {
      Log::error("Vonage WhatsApp error: " . $e->getMessage());
      return false;
    }
  }
}
