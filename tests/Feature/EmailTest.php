<?php

namespace Tests\Feature;

use App\Mail\GuestRsvpInviteMail;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailTest extends TestCase
{
  use RefreshDatabase;

  public function test_guest_rsvp_email_can_be_sent()
  {
    Mail::fake();

    // Create a category first
    $category = Category::create([
      'name' => 'Wedding',
      'slug' => 'wedding',
    ]);

    // Create a customer
    $customer = Customer::factory()->create([
      'first_name' => 'John',
      'last_name' => 'Doe',
    ]);

    // Create an event
    $event = Event::factory()->create([
      'customer_id' => $customer->id,
      'category_id' => $category->id,
      'name' => 'Test Event',
    ]);

    // Create a guest
    $guest = Guest::factory()->create([
      'customer_id' => $customer->id,
      'first_name' => 'Jane',
      'last_name' => 'Smith',
      'email' => 'jane@example.com',
    ]);

    // Attach guest to event
    $guest->events()->attach($event->id, [
      'rsvp_token' => 'test-token-123',
      'rsvp_sent_at' => now(),
      'rsvp_expires_at' => now()->addDays(7),
      'attendance_status' => 'invited',
    ]);

    // Reload guest with relationships
    $guest->loadMissing(['customer', 'events']);

    // Send the email
    Mail::send(new GuestRsvpInviteMail($guest));

    // Assert that an email was sent
    Mail::assertSent(GuestRsvpInviteMail::class, function ($mail) use ($guest) {
      return $mail->guest->id === $guest->id;
    });
  }

  public function test_guest_rsvp_email_content()
  {
    Mail::fake();

    // Create a category first
    $category = Category::create([
      'name' => 'Wedding',
      'slug' => 'wedding',
    ]);

    // Create test data
    $customer = Customer::factory()->create([
      'first_name' => 'John',
      'last_name' => 'Doe',
    ]);

    $event = Event::factory()->create([
      'customer_id' => $customer->id,
      'category_id' => $category->id,
      'name' => 'Birthday Party',
    ]);

    $guest = Guest::factory()->create([
      'customer_id' => $customer->id,
      'title' => 'Ms.',
      'first_name' => 'Jane',
      'last_name' => 'Smith',
      'email' => 'jane@example.com',
    ]);

    $guest->events()->attach($event->id, [
      'rsvp_token' => 'test-token-123',
      'rsvp_sent_at' => now(),
      'rsvp_expires_at' => now()->addDays(7),
      'attendance_status' => 'invited',
    ]);

    $guest->loadMissing(['customer', 'events']);

    // Create and render the email
    $mailable = new GuestRsvpInviteMail($guest);
    $content = $mailable->render();

    // Assert email contains expected content
    $this->assertStringContainsString('Hello Ms. Jane Smith', $content);
    $this->assertStringContainsString('John Doe', $content);
    $this->assertStringContainsString('Birthday Party', $content);
    $this->assertStringContainsString('test-token-123', $content);
    $this->assertStringContainsString('Confirm RSVP', $content);
  }
}
