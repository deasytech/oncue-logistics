<?php

namespace Tests\Feature;

use App\Filament\Resources\BulkMessageResource\Pages\ListBulkMessages;
use App\Filament\Resources\BulkMessageResource\Pages\ViewBulkMessage;
use App\Filament\Resources\BulkMessageResource\RelationManagers\DeliveriesRelationManager;
use App\Jobs\SendBulkGuestMessageJob;
use App\Models\BulkMessage;
use App\Models\Customer;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BulkMessageResourceSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_view_and_compose_flow(): void
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        DB::table('categories')->insert(['id' => 1, 'name' => 'Test Category', 'created_at' => now(), 'updated_at' => now()]);

        $customer = Customer::factory()->create();
        $guestWithEmailAndPhone = Guest::factory()->create(['customer_id' => $customer->id]);
        $guestWithNoContactInfo = Guest::factory()->create(['customer_id' => $customer->id, 'email' => null, 'phone' => null]);

        $existingMessage = BulkMessage::create([
            'customer_id' => $customer->id,
            'title' => 'Existing Broadcast',
            'body' => '<p>Hello {{first_name}}</p>',
            'channels' => ['email', 'sms'],
            'total_recipients' => 1,
        ]);

        Livewire::test(ListBulkMessages::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$existingMessage]);

        Livewire::test(ViewBulkMessage::class, ['record' => $existingMessage->getRouteKey()])
            ->assertSuccessful();

        Livewire::test(DeliveriesRelationManager::class, [
            'ownerRecord' => $existingMessage,
            'pageClass' => ViewBulkMessage::class,
        ])->assertSuccessful();

        Queue::fake();

        Livewire::test(ListBulkMessages::class)
            ->callAction('composeBulkMessage', [
                'customer_id' => $customer->id,
                'event_id' => null,
                'channels' => ['email', 'sms'],
                'title' => 'Feedback Survey',
                'body' => '<p>Hello {{first_name}}! Thanks for coming.</p>',
                'send_test' => false,
            ])
            ->assertHasNoActionErrors();

        $newMessage = BulkMessage::where('title', 'Feedback Survey')->first();

        $this->assertNotNull($newMessage);
        $this->assertSame($customer->id, $newMessage->customer_id);
        // Only guests with usable contact info for the selected channels are targeted.
        $this->assertSame(1, $newMessage->total_recipients);

        Queue::assertPushed(SendBulkGuestMessageJob::class, function (SendBulkGuestMessageJob $job) use ($newMessage, $guestWithEmailAndPhone) {
            return $job->bulkMessageId === $newMessage->id && $job->guestId === $guestWithEmailAndPhone->id;
        });

        Queue::assertNotPushed(SendBulkGuestMessageJob::class, function (SendBulkGuestMessageJob $job) use ($guestWithNoContactInfo) {
            return $job->guestId === $guestWithNoContactInfo->id;
        });
    }
}
