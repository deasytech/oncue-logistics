<?php

namespace App\Console\Commands;

use App\Services\TwilioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestCommunicationServices extends Command
{
  protected $signature = 'test:communications 
                            {--email= : Email address to send test email to}
                            {--phone= : Phone number to send test SMS/WhatsApp to}
                            {--test-sms : Test SMS only}
                            {--test-whatsapp : Test WhatsApp only}
                            {--test-email : Test email only}';

  protected $description = 'Test email SMTP and SMS (Twilio) configurations';

  public function handle(TwilioService $twilioService): int
  {
    $testSms = $this->option('test-sms');
    $testWhatsapp = $this->option('test-whatsapp');
    $testEmail = $this->option('test-email');
    $email = $this->option('email');
    $phone = $this->option('phone');

    // If no specific test selected, test all
    if (!$testSms && !$testWhatsapp && !$testEmail) {
      $testSms = true;
      $testWhatsapp = true;
      $testEmail = true;
    }

    $allPassed = true;

    // Test Email
    if ($testEmail) {
      if (!$email) {
        $email = $this->ask('Enter email address to send test email to');
      }

      $this->info("\n--- Testing Email Configuration ---");
      $this->info("SMTP Host: " . config('mail.mailers.smtp.host'));
      $this->info("SMTP Port: " . config('mail.mailers.smtp.port'));
      $this->info("From Address: " . config('mail.from.address'));
      $this->info("Sending test email to: $email");

      try {
        Mail::raw('This is a test email from ' . config('app.name') . '. If you received this, your SMTP configuration is working correctly.', function ($message) use ($email) {
          $message->to($email)
            ->subject('Test Email - ' . config('app.name'));
        });

        $this->info("✓ Test email sent successfully!");
      } catch (\Exception $e) {
        $this->error("✗ Failed to send test email: " . $e->getMessage());
        Log::error("Test email failed: " . $e->getMessage());
        $allPassed = false;
      }
    }

    // Test SMS
    if ($testSms) {
      if (!$phone) {
        $phone = $this->ask('Enter phone number to send test SMS to (e.g., +2348012345678)');
      }

      $this->info("\n--- Testing SMS Configuration (Twilio) ---");
      $this->info("Account SID: " . substr(config('services.twilio.sid'), 0, 10) . '...');
      $this->info("Sender ID: " . (config('services.twilio.sender_id') ?: 'Not configured'));
      $this->info("Messaging Service SID: " . (config('services.twilio.messaging_service_sid') ?: 'Not configured'));
      $this->info("Phone Number: " . (config('services.twilio.from') ?: 'Not configured'));
      $this->info("Sending test SMS to: $phone");

      $result = $twilioService->sendSms($phone, 'This is a test SMS from ' . config('app.name') . '. Your Twilio SMS configuration is working correctly.');

      if ($result) {
        $this->info("✓ Test SMS sent successfully!");
      } else {
        $this->error("✗ Failed to send test SMS. Check logs for details.");
        $allPassed = false;
      }
    }

    // Test WhatsApp
    if ($testWhatsapp) {
      if (!$phone) {
        $phone = $this->ask('Enter phone number to send test WhatsApp to (e.g., +2348012345678)');
      }

      $this->info("\n--- Testing WhatsApp Configuration (Twilio) ---");
      $this->info("Account SID: " . substr(config('services.twilio.sid'), 0, 10) . '...');
      $this->info("WhatsApp Number: " . (config('services.twilio.whatsapp_from') ?: 'Not configured'));
      $this->info("Sending test WhatsApp to: $phone");

      $result = $twilioService->sendWhatsApp($phone, 'This is a test WhatsApp message from ' . config('app.name') . '. Your Twilio WhatsApp configuration is working correctly.');

      if ($result) {
        $this->info("✓ Test WhatsApp sent successfully!");
      } else {
        $this->error("✗ Failed to send test WhatsApp. Check logs for details.");
        $allPassed = false;
      }
    }

    // Summary
    $this->info("\n--- Test Summary ---");
    if ($allPassed) {
      $this->info("All tests passed! ✓");
      return Command::SUCCESS;
    } else {
      $this->error("Some tests failed. Please check the configuration and logs.");
      return Command::FAILURE;
    }
  }
}
