<?php

namespace Tests\Feature;

use App\Jobs\SendBulkGuestMessageJob;
use App\Mail\NewsletterMail;
use App\Models\BulkMessage;
use App\Models\BulkMessageDelivery;
use App\Models\Customer;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendBulkGuestMessageJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_channel_personalizes_and_marks_delivery_sent(): void
    {
        Mail::fake();

        $customer = Customer::factory()->create();
        $guest = Guest::factory()->create(['customer_id' => $customer->id, 'first_name' => 'Ada']);

        $bulkMessage = BulkMessage::create([
            'customer_id' => $customer->id,
            'title' => 'Feedback Survey',
            'body' => '<p>Hello {{first_name}}! Thanks for coming.</p>',
            'channels' => ['email'],
            'total_recipients' => 1,
        ]);

        (new SendBulkGuestMessageJob($bulkMessage->id, $guest->id))->handle(app(\App\Services\TwilioService::class));

        Mail::assertQueued(NewsletterMail::class, function (NewsletterMail $mail) use ($guest) {
            return $mail->hasTo($guest->email)
                && $mail->subject === 'Feedback Survey'
                && str_contains($mail->content, 'Hello Ada!');
        });

        $delivery = BulkMessageDelivery::where('bulk_message_id', $bulkMessage->id)
            ->where('guest_id', $guest->id)
            ->where('channel', 'email')
            ->first();

        $this->assertSame('sent', $delivery->status);
        $this->assertNotNull($delivery->sent_at);
    }

    public function test_email_channel_skipped_when_guest_has_no_email(): void
    {
        Mail::fake();

        $customer = Customer::factory()->create();
        $guest = Guest::factory()->create(['customer_id' => $customer->id, 'email' => null]);

        $bulkMessage = BulkMessage::create([
            'customer_id' => $customer->id,
            'title' => 'Feedback Survey',
            'body' => '<p>Hello {{first_name}}!</p>',
            'channels' => ['email'],
            'total_recipients' => 1,
        ]);

        (new SendBulkGuestMessageJob($bulkMessage->id, $guest->id))->handle(app(\App\Services\TwilioService::class));

        Mail::assertNothingQueued();

        $delivery = BulkMessageDelivery::where('bulk_message_id', $bulkMessage->id)
            ->where('guest_id', $guest->id)
            ->where('channel', 'email')
            ->first();

        $this->assertSame('skipped', $delivery->status);
    }

    public function test_already_resolved_delivery_is_not_reprocessed_on_retry(): void
    {
        Mail::fake();

        $customer = Customer::factory()->create();
        $guest = Guest::factory()->create(['customer_id' => $customer->id]);

        $bulkMessage = BulkMessage::create([
            'customer_id' => $customer->id,
            'title' => 'Feedback Survey',
            'body' => '<p>Hello {{first_name}}!</p>',
            'channels' => ['email'],
            'total_recipients' => 1,
        ]);

        BulkMessageDelivery::create([
            'bulk_message_id' => $bulkMessage->id,
            'guest_id' => $guest->id,
            'channel' => 'email',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        (new SendBulkGuestMessageJob($bulkMessage->id, $guest->id))->handle(app(\App\Services\TwilioService::class));

        Mail::assertNothingQueued();
    }
}
