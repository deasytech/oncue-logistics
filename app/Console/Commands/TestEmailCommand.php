<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'mail:test {email? : The email address to send test to}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send a test email to verify mail configuration';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $recipient = $this->argument('email') ?? 'test@example.com';

    $this->info('Testing email configuration...');
    $this->info('Mailer: ' . config('mail.default'));
    $this->info('Host: ' . config('mail.mailers.smtp.host'));
    $this->info('Port: ' . config('mail.mailers.smtp.port'));
    $this->info('From: ' . config('mail.from.address'));
    $this->info('To: ' . $recipient);
    $this->newLine();

    try {
      Mail::raw('This is a test email from Oncue Logistics. If you received this, your email configuration is working correctly!', function ($message) use ($recipient) {
        $message->to($recipient)
          ->subject('Test Email from Oncue Logistics');
      });

      $this->info('✅ Test email sent successfully!');
      $this->info('Check your inbox at: ' . $recipient);

      return Command::SUCCESS;
    } catch (\Exception $e) {
      $this->error('❌ Failed to send test email!');
      $this->error('Error: ' . $e->getMessage());

      return Command::FAILURE;
    }
  }
}
