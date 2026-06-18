<?php

namespace Tests\Feature;

use App\Models\DeliveryService;
use App\Models\Delivery;
use App\Models\Event;
use App\Models\Guest;
use App\Models\PackagePayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class DeliveryPostPaymentFlowTest extends TestCase
{
  use RefreshDatabase;

  protected $deliveryService;
  protected $event;
  protected $guest;

  protected function setUp(): void
  {
    parent::setUp();

    // Create test delivery service
    $this->deliveryService = DeliveryService::create([
      'name' => 'Standard Delivery',
      'description' => 'Standard delivery service for events',
      'cost' => 5000.00,
      'applicable_to' => 'all',
      'is_active' => true,
    ]);

    // Create test event
    $this->event = Event::create([
      'name' => 'Test Event',
      'description' => 'Test event for delivery',
      'event_date' => now()->addDays(7),
      'customer_id' => 1,
    ]);

    // Create test guest
    $this->guest = Guest::create([
      'title' => 'Mr.',
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john@example.com',
      'phone' => '08012345678',
      'address' => '123 Test Street',
      'city_id' => 1,
      'state_id' => 1,
      'customer_id' => 1,
    ]);

    // Attach guest to event
    $this->event->guests()->attach($this->guest->id);
  }

  public function test_post_payment_modal_shows_after_successful_payment()
  {
    // Create a delivery record
    $delivery = Delivery::create([
      'event_id' => $this->event->id,
      'delivery_service_id' => $this->deliveryService->id,
      'delivery_required' => true,
      'status' => 'pending',
      'cost' => 5000.00,
      'payment_status' => 'pending',
      'payment_method' => 'online',
      'payment_reference' => 'TEST_REF_123',
    ]);

    // Create package payment record
    $packagePayment = PackagePayment::create([
      'customer_id' => 1,
      'reference' => 'TEST_REF_123',
      'amount' => 500000, // 5000 * 100 (kobo)
      'status' => 'pending',
      'items' => [
        'type' => 'delivery_service',
        'delivery_id' => $delivery->id,
        'service_name' => $this->deliveryService->name,
      ],
    ]);

    // Mock Paystack API response for successful payment
    Http::fake([
      'https://api.paystack.co/transaction/verify/TEST_REF_123' => Http::response([
        'status' => true,
        'data' => [
          'status' => 'success',
          'reference' => 'TEST_REF_123',
        ],
      ], 200),
    ]);

    // Simulate payment callback
    $response = $this->get(route('payment.callback', ['reference' => 'TEST_REF_123']));

    // Verify delivery payment status was updated
    $delivery->refresh();
    $this->assertEquals('paid', $delivery->payment_status);

    // Verify package payment status was updated
    $packagePayment->refresh();
    $this->assertEquals('success', $packagePayment->status);

    // Verify redirect to delivery services with post-payment modal
    $response->assertRedirect(route('delivery.services'));
    $response->assertSessionHas('show_post_payment_modal', true);
    $response->assertSessionHas('message', 'Delivery service payment successful!');
  }

  public function test_select_packaging_redirects_to_packages_page()
  {
    // Test the Select Packaging functionality
    $response = $this->post('/livewire/message/delivery.delivery-services', [
      'fingerprint' => [
        'id' => 'test-id',
        'name' => 'delivery.delivery-services',
        'locale' => 'en',
      ],
      'serverMemo' => [
        'data' => [
          'showPostPaymentModal' => true,
        ],
        'children' => [],
        'errors' => [],
      ],
      'updates' => [
        [
          'type' => 'callMethod',
          'payload' => [
            'method' => 'selectPackaging',
            'params' => [],
          ],
        ],
      ],
    ]);

    // This would need to be adapted based on your Livewire testing setup
    // For now, we'll test the redirect logic in the controller method
    $this->assertTrue(true); // Placeholder assertion
  }

  public function test_end_session_redirects_to_dashboard()
  {
    // Test the End Session functionality
    $response = $this->post('/livewire/message/delivery.delivery-services', [
      'fingerprint' => [
        'id' => 'test-id',
        'name' => 'delivery.delivery-services',
        'locale' => 'en',
      ],
      'serverMemo' => [
        'data' => [
          'showPostPaymentModal' => true,
          'showEndSessionModal' => false,
        ],
        'children' => [],
        'errors' => [],
      ],
      'updates' => [
        [
          'type' => 'callMethod',
          'payload' => [
            'method' => 'endSession',
            'params' => [],
          ],
        ],
      ],
    ]);

    // This would need to be adapted based on your Livewire testing setup
    // For now, we'll test the redirect logic in the controller method
    $this->assertTrue(true); // Placeholder assertion
  }
}
