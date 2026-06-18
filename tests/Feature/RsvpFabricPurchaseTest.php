<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\FabricType;
use App\Models\Guest;
use App\Models\State;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RsvpFabricPurchaseTest extends TestCase
{
  use RefreshDatabase;

  protected $state;
  protected $city;
  protected $fabricType;
  protected $guest;
  protected $event;

  protected function setUp(): void
  {
    parent::setUp();

    // Ensure a customer exists for FK constraints
    $customer = \App\Models\Customer::factory()->create();

    // Ensure a category exists for events
    $category = \App\Models\Category::first() ?? \App\Models\Category::create(['name' => 'Test Category']);

    // Create test state and city
    $this->state = State::create([
      'name' => 'Lagos',
      'is_active' => true,
    ]);

    $this->city = City::create([
      'name' => 'Ikeja',
      'state_id' => $this->state->id,
      'is_active' => true,
    ]);

    // Create test fabric type
    $this->fabricType = FabricType::create([
      'name' => 'Ankara',
      'description' => 'Traditional Nigerian fabric',
      'base_price' => 5000.00,
      'is_active' => true,
    ]);

    // Create test guest
    $this->guest = Guest::create([
      'title' => 'Mr.',
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john@example.com',
      'phone' => '08012345678',
      'address' => '123 Test Street',
      'city_id' => $this->city->id,
      'state_id' => $this->state->id,
      'customer_id' => $customer->id,
    ]);

    // Create test event
    $this->event = Event::create([
      'name' => 'Test Event',
      'description' => 'Test event for RSVP',
      'event_date' => now()->addDays(7),
      'customer_id' => $customer->id,
      'category_id' => $category->id,
      'location' => 'Test Location',
      'estimated_number_of_guest' => 10,
    ]);

    // Attach fabric type to event
    $this->event->fabricTypes()->attach($this->fabricType->id, [
      'custom_price' => 5000.00,
    ]);

    // Attach guest to event with RSVP token
    $this->event->guests()->attach($this->guest->id, [
      'rsvp_token' => 'test-rsvp-token',
      'rsvp_sent_at' => now(),
      'rsvp_expires_at' => now()->addDays(7),
      'attendance_status' => 'invited',
    ]);
  }

  public function test_declined_guest_can_skip_fabric_purchase()
  {
    $response = $this->post(route('rsvp.submit', 'test-rsvp-token'), [
      'attendance_status' => 'declined',
      'fabric_purchase_interest' => 'no',
      'plus_one' => 'Jane Doe',
    ]);

    $response->assertRedirect(route('rsvp.show', 'test-rsvp-token'));
    $response->assertSessionHas('success', 'Your response has been recorded. Thank you!');

    // Verify the guest's attendance status was updated
    $this->guest->refresh();
    $pivot = $this->event->guests()->where('guest_id', $this->guest->id)->first()->pivot;
    $this->assertEquals('declined', $pivot->attendance_status);
    $this->assertEquals('Jane Doe', $pivot->plus_one);
    $this->assertNotNull($pivot->rsvp_responded_at);
  }

  public function test_declined_guest_can_purchase_fabric()
  {
    $response = $this->post(route('rsvp.submit', 'test-rsvp-token'), [
      'attendance_status' => 'declined',
      'fabric_purchase_interest' => 'yes',
      'fabric_types' => [$this->fabricType->id],
      'payment_method' => 'online',
      'plus_one' => 'Jane Doe',
    ]);

    $response->assertRedirect(route('payment.summary', [
      'token' => 'test-rsvp-token',
      'order_id' => 1
    ]));
    $response->assertSessionHas('success', 'Please review your order details before proceeding to payment.');

    // Verify the guest's attendance status was updated
    $pivot = $this->event->guests()->where('guest_id', $this->guest->id)->first()->pivot;
    $this->assertEquals('declined', $pivot->attendance_status);
    $this->assertEquals('Jane Doe', $pivot->plus_one);
    $this->assertNotNull($pivot->rsvp_responded_at);

    // Verify fabric selection was created
    $this->assertDatabaseHas('guest_fabric_selections', [
      'guest_id' => $this->guest->id,
      'event_id' => $this->event->id,
      'payment_status' => 'pending',
      'payment_method' => 'online',
    ]);
  }

  public function test_declined_guest_validation_requires_fabric_purchase_interest()
  {
    // Controller assumes 'no' if declined and fabric_purchase_interest not provided
    $response = $this->post(route('rsvp.submit', 'test-rsvp-token'), [
      'attendance_status' => 'declined',
      'plus_one' => 'Jane Doe',
    ]);

    $response->assertRedirect(route('rsvp.show', 'test-rsvp-token'));
    $response->assertSessionHas('success', 'Your response has been recorded. Thank you!');

    $pivot = $this->event->guests()->where('guest_id', $this->guest->id)->first()->pivot;
    $this->assertEquals('declined', $pivot->attendance_status);
  }

  public function test_declined_guest_validation_requires_fabric_selection_when_interested()
  {
    $response = $this->post(route('rsvp.submit', 'test-rsvp-token'), [
      'attendance_status' => 'declined',
      'fabric_purchase_interest' => 'yes',
      'payment_method' => 'online',
      'plus_one' => 'Jane Doe',
    ]);

    $response->assertSessionHasErrors(['fabric_types']);
    $response->assertSessionHasErrors(['fabric_types' => 'The fabric types field is required.']);
  }

  public function test_confirmed_guest_can_select_fabric_normally()
  {
    $response = $this->post(route('rsvp.submit', 'test-rsvp-token'), [
      'attendance_status' => 'confirmed',
      'fabric_types' => [$this->fabricType->id],
      'payment_method' => 'online',
      'plus_one' => 'Jane Doe',
    ]);

    $response->assertRedirect(route('payment.summary', [
      'token' => 'test-rsvp-token',
      'order_id' => 1
    ]));
    $response->assertSessionHas('success', 'Please review your order details before proceeding to payment.');

    // Verify the guest's attendance status was updated
    $pivot = $this->event->guests()->where('guest_id', $this->guest->id)->first()->pivot;
    $this->assertEquals('confirmed', $pivot->attendance_status);
    $this->assertEquals('Jane Doe', $pivot->plus_one);
    $this->assertNotNull($pivot->rsvp_responded_at);

    // Verify fabric selection was created
    $this->assertDatabaseHas('guest_fabric_selections', [
      'guest_id' => $this->guest->id,
      'event_id' => $this->event->id,
      'payment_status' => 'pending',
      'payment_method' => 'online',
    ]);
  }
}
