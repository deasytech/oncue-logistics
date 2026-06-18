<?php

namespace App\Filament\Resources\CustomerResource\Actions;

use App\Mail\NewsletterMail;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsletterAction extends BulkAction
{
  protected function setUp(): void
  {
    parent::setUp();

    $this->name('sendNewsletter');
    $this->label('Send Newsletter');
    $this->icon('heroicon-o-paper-airplane');
    $this->color('primary');
    $this->requiresConfirmation();
    $this->modalHeading('Send Newsletter to Selected Customers');
    $this->modalDescription('Create and send a newsletter email to the selected customers.');
    $this->modalSubmitActionLabel('Send Newsletter');
    $this->modalWidth('5xl');

    $this->form([
      TextInput::make('subject')
        ->label('Newsletter Subject')
        ->placeholder('Enter newsletter subject')
        ->required()
        ->maxLength(255),

      RichEditor::make('content')
        ->label('Newsletter Content')
        ->placeholder('Write your newsletter content here...')
        ->required()
        ->toolbarButtons([
          'bold',
          'italic',
          'underline',
          'strike',
          'link',
          'orderedList',
          'unorderedList',
          'h2',
          'h3',
          'blockquote',
        ]),

      TextInput::make('call_to_action_text')
        ->label('Call to Action Text (Optional)')
        ->placeholder('e.g., Learn More, Shop Now, Get Started')
        ->maxLength(50),

      TextInput::make('call_to_action_url')
        ->label('Call to Action URL (Optional)')
        ->placeholder('https://example.com')
        ->url()
        ->maxLength(255),

      Toggle::make('send_test')
        ->label('Send Test Email First')
        ->helperText('Send a test email to yourself before sending to all customers')
        ->default(true),
    ]);

    $this->action(function (Collection $records, array $data) {
      $subject = $data['subject'];
      $content = $data['content'];
      $callToActionText = $data['call_to_action_text'] ?? null;
      $callToActionUrl = $data['call_to_action_url'] ?? null;
      $sendTest = $data['send_test'] ?? true;

      // If test email is requested, send to current user first
      if ($sendTest) {
        $testEmail = filament()->auth()->user()->email;

        try {
          Mail::to($testEmail)->send(new NewsletterMail(
            $subject,
            $content,
            $callToActionText,
            $callToActionUrl
          ));

          Notification::make()
            ->title('Test email sent successfully!')
            ->body('Please check your email and confirm the newsletter looks good before sending to customers.')
            ->success()
            ->send();

          return;
        } catch (\Exception $e) {
          Notification::make()
            ->title('Test email failed!')
            ->body('Error: ' . $e->getMessage())
            ->danger()
            ->send();

          return;
        }
      }

      // Send to all selected customers
      $sentCount = 0;
      $failedCount = 0;

      foreach ($records as $customer) {
        try {
          if ($customer->email) {
            Mail::to($customer->email)->send(new NewsletterMail(
              $subject,
              $content,
              $callToActionText,
              $callToActionUrl
            ));
            $sentCount++;
          }
        } catch (\Exception $e) {
          $failedCount++;
          Log::error('Failed to send newsletter to customer ' . $customer->id . ': ' . $e->getMessage());
        }
      }

      $totalCustomers = $records->count();

      if ($failedCount === 0) {
        Notification::make()
          ->title('Newsletter sent successfully!')
          ->body("Successfully sent newsletter to {$sentCount} customers.")
          ->success()
          ->send();
      } else {
        Notification::make()
          ->title('Newsletter sent with some issues')
          ->body("Sent to {$sentCount} customers. Failed to send to {$failedCount} customers.")
          ->warning()
          ->send();
      }
    });
  }

  public static function make(?string $name = null): static
  {
    $static = app(static::class, ['name' => $name ?? 'sendNewsletter']);
    $static->configure();

    return $static;
  }
}
