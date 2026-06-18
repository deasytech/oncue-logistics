<?php

namespace App\Console\Commands;

use App\Services\TwilioService;
use Illuminate\Console\Command;

class SendSampleEventSms extends Command
{
  protected $signature = 'send:sample-event-sms 
                            {--numbers= : Comma-separated phone numbers}';

  protected $description = 'Send sample event invitation SMS to specified phone numbers';

  public function handle(TwilioService $twilioService): int
  {
    $numbersOption = $this->option('numbers');

    $numbers = $numbersOption
      ? explode(',', $numbersOption)
      : ['+2348068341041', '+2349078337079'];

    // Sample event invitation message
    $eventName = 'Dele & Funmi\'s Wedding';
    $guestName = 'John Doe';
    $sampleRsvpLink = 'https://oncue.test/rsvp/a1b2c3d4e5f6';

    $message = "Hi {$guestName}, you're invited to {$eventName}. Please RSVP: {$sampleRsvpLink}";

    $this->info("Sending sample event invitation SMS...");
    $this->info("Event: {$eventName}");
    $this->info("Message: {$message}");
    $this->info("");

    $allSent = true;

    foreach ($numbers as $number) {
      $number = trim($number);

      $this->info("Sending to: {$number}");

      $result = $twilioService->sendSms($number, $message);

      if ($result) {
        $this->info("✓ SMS sent successfully to {$number}");
      } else {
        $this->error("✗ Failed to send SMS to {$number}");
        $allSent = false;
      }

      $this->info("");
    }

    if ($allSent) {
      $this->info("All sample SMS messages sent successfully!");
      return Command::SUCCESS;
    } else {
      $this->error("Some SMS messages failed to send.");
      return Command::FAILURE;
    }
  }
}
