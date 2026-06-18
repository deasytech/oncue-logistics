<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\DeliveryService;
use App\Models\Event;
use App\Models\Delivery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestDeliveryFlow extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'test:delivery-flow {customer_id}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Test the delivery service flow for a customer';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $customerId = $this->argument('customer_id');
    $customer = Customer::find($customerId);

    if (!$customer) {
      $this->error('Customer not found');
      return 1;
    }

    $this->info('Testing delivery flow for customer: ' . ($customer->user->name ?? 'Unknown'));

    // Check if delivery services exist
    $deliveryServices = DeliveryService::all();
    $this->info('Available delivery services: ' . $deliveryServices->count());

    foreach ($deliveryServices as $service) {
      $this->line('- ' . $service->name . ' (₦' . number_format($service->cost, 2) . ')');
    }

    // Check if customer has events
    $events = Event::where('customer_id', $customer->id)->get();
    $this->info('Customer events: ' . $events->count());

    if ($events->isEmpty()) {
      $this->warn('Customer has no events. Create an event first.');
      return 1;
    }

    // Check delivery status for each event
    foreach ($events as $event) {
      $delivery = Delivery::where('event_id', $event->id)->first();

      if ($delivery) {
        $this->info('Event: ' . $event->name);
        $this->line('  Delivery Required: ' . ($delivery->delivery_required ? 'Yes' : 'No'));
        $this->line('  Payment Status: ' . $delivery->payment_status);
        $this->line('  Cost: ₦' . number_format($delivery->cost, 2));

        if ($delivery->delivery_service_id) {
          $this->line('  Service: ' . $delivery->deliveryService->name);
        }
      } else {
        $this->warn('Event: ' . $event->name . ' - No delivery record found');
      }
    }

    // Check guest access
    $hasPaidDelivery = Delivery::whereHas('event', function ($query) use ($customer) {
      $query->where('customer_id', $customer->id);
    })
      ->where('delivery_required', true)
      ->where('payment_status', 'paid')
      ->exists();

    $this->info('Guest Management Access: ' . ($hasPaidDelivery ? 'GRANTED' : 'RESTRICTED'));

    $this->info('Delivery flow test completed successfully!');
    return 0;
  }
}
