<?php

namespace Tests\Feature;

use App\Filament\Resources\TwilioMessageLogResource\Pages\ListTwilioMessageLogs;
use App\Filament\Resources\TwilioMessageLogResource\Pages\ViewTwilioMessageLog;
use App\Models\Event;
use App\Models\Guest;
use App\Models\TwilioMessageLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TwilioMessageLogResourceSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_and_view_pages_render_and_retry_works(): void
    {
        // Defense in depth: even though the retry action isn't invoked below, make sure
        // this test can never reach the real Twilio API if the client were resolved.
        config(['services.twilio.sid' => null, 'services.twilio.token' => null]);

        Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        DB::table('categories')->insert(['id' => 1, 'name' => 'Test Category', 'created_at' => now(), 'updated_at' => now()]);

        $guest = Guest::factory()->create();
        $event = Event::factory()->create();

        $failedLog = TwilioMessageLog::create([
            'channel' => 'whatsapp_template',
            'to' => '+2348012345678',
            'to_country' => '234',
            'context' => 'rsvp_invite',
            'content_sid' => 'HXtest123',
            'message_sid' => null,
            'status' => 'failed',
            'error_code' => 63049,
            'error_message' => 'Blocked by Meta',
            'payload' => [
                'guest_name' => 'Test Guest',
                'event_name' => 'Test Event',
                'event_date' => 'Jan 1, 2026',
                'rsvp_token' => 'tok123',
                'customer_name' => 'Test Customer',
            ],
            'guest_id' => $guest->id,
            'event_id' => $event->id,
        ]);

        TwilioMessageLog::create([
            'channel' => 'sms',
            'to' => '+2348012345679',
            'to_country' => '234',
            'context' => 'rsvp_invite',
            'status' => 'sent',
            'message_sid' => 'SMtest',
        ]);

        Livewire::test(ListTwilioMessageLogs::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$failedLog]);

        Livewire::test(ListTwilioMessageLogs::class)
            ->filterTable('channel', 'whatsapp_template')
            ->assertCanSeeTableRecords([$failedLog]);

        Livewire::test(ListTwilioMessageLogs::class)
            ->filterTable('error_code', 63049)
            ->assertCanSeeTableRecords([$failedLog]);

        Livewire::test(ViewTwilioMessageLog::class, ['record' => $failedLog->getRouteKey()])
            ->assertSuccessful();

        $this->assertTrue($failedLog->isRetryable());

        // Retry should be offered on the failed row and hidden on the already-sent row.
        // The action itself isn't invoked here since that would attempt a real Twilio send.
        Livewire::test(ListTwilioMessageLogs::class)
            ->assertTableActionVisible('retry', $failedLog)
            ->assertTableActionHidden('retry', TwilioMessageLog::where('status', 'sent')->first());
    }
}
