<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Package;
use App\Models\PackageColor;
use App\Models\PackageCustomization;
use App\Models\PackageFont;
use App\Models\PackageMaterial;
use App\Models\DeliveryService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageCustomizationEventTest extends TestCase
{
  use RefreshDatabase;

  public function test_package_customization_can_have_multiple_events()
  {
    // Create a user and customer
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['user_id' => $user->id]);

    // Create a category for events
    $category = Category::create([
      'name' => 'Wedding',
      'description' => 'Wedding events',
    ]);

    // Create events for the customer with proper category
    $event1 = Event::factory()->create([
      'customer_id' => $customer->id,
      'category_id' => $category->id,
      'name' => 'Wedding Ceremony',
      'is_active' => true,
    ]);

    $event2 = Event::factory()->create([
      'customer_id' => $customer->id,
      'category_id' => $category->id,
      'name' => 'Reception Party',
      'is_active' => true,
    ]);

    // Create package and related data
    $package = Package::create([
      'name' => 'Premium Wedding Package',
      'description' => 'Complete wedding package',
      'base_price' => 100.00,
      'sku' => 'PREMIUM-WEDDING-001',
      'is_active' => true,
    ]);
    $material = PackageMaterial::create([
      'package_id' => $package->id,
      'name' => 'Premium Paper',
      'price_modifier' => 25.00,
    ]);
    $font = PackageFont::create([
      'package_id' => $package->id,
      'name' => 'Elegant Script',
      'price_modifier' => 15.00,
    ]);
    $color = PackageColor::create([
      'package_id' => $package->id,
      'name' => 'Gold',
      'hex' => '#FFD700',
      'price_modifier' => 10.00,
    ]);
    $deliveryService = DeliveryService::create([
      'name' => 'Standard Delivery',
      'cost' => 20.00,
      'is_active' => true,
    ]);

    // Create package customization
    $customization = PackageCustomization::create([
      'package_id' => $package->id,
      'customer_id' => $customer->id,
      'material_id' => $material->id,
      'font_id' => $font->id,
      'color_id' => $color->id,
      'message' => 'Test message',
      'location' => 'Test location',
      'quantity' => 1,
      'unit_price' => 150.00,
      'total_price' => 150.00,
      'status' => 'in_cart',
      'delivery_service_id' => $deliveryService->id,
    ]);

    // Attach events to the customization
    $customization->events()->attach([$event1->id, $event2->id]);

    // Assert that the customization has both events
    $this->assertCount(2, $customization->events);
    $this->assertTrue($customization->events->contains($event1));
    $this->assertTrue($customization->events->contains($event2));
  }

  public function test_events_can_be_detached_from_package_customization()
  {
    // Create a user and customer
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['user_id' => $user->id]);

    // Create a category for events
    $category = Category::create([
      'name' => 'Party',
      'description' => 'Party events',
    ]);

    // Create events for the customer with proper category
    $event1 = Event::factory()->create([
      'customer_id' => $customer->id,
      'category_id' => $category->id,
      'is_active' => true
    ]);
    $event2 = Event::factory()->create([
      'customer_id' => $customer->id,
      'category_id' => $category->id,
      'is_active' => true
    ]);

    // Create package and related data
    $package = Package::create([
      'name' => 'Premium Party Package',
      'description' => 'Complete party package',
      'base_price' => 80.00,
      'sku' => 'PREMIUM-PARTY-001',
      'is_active' => true,
    ]);
    $material = PackageMaterial::create([
      'package_id' => $package->id,
      'name' => 'Standard Paper',
      'price_modifier' => 20.00,
    ]);
    $font = PackageFont::create([
      'package_id' => $package->id,
      'name' => 'Modern Script',
      'price_modifier' => 12.00,
    ]);
    $color = PackageColor::create([
      'package_id' => $package->id,
      'name' => 'Silver',
      'hex' => '#C0C0C0',
      'price_modifier' => 8.00,
    ]);
    $deliveryService = DeliveryService::create([
      'name' => 'Express Delivery',
      'cost' => 25.00,
      'is_active' => true,
    ]);

    // Create package customization with events
    $customization = PackageCustomization::create([
      'package_id' => $package->id,
      'customer_id' => $customer->id,
      'material_id' => $material->id,
      'font_id' => $font->id,
      'color_id' => $color->id,
      'message' => 'Party message',
      'location' => 'Party location',
      'quantity' => 1,
      'unit_price' => 120.00,
      'total_price' => 120.00,
      'status' => 'in_cart',
      'delivery_service_id' => $deliveryService->id,
    ]);

    // Attach events
    $customization->events()->attach([$event1->id, $event2->id]);
    $this->assertCount(2, $customization->events);

    // Detach one event
    $customization->events()->detach($event1->id);

    // Refresh the relationship to get updated data
    $customization->refresh();

    // Assert only one event remains
    $this->assertCount(1, $customization->events);
    $this->assertTrue($customization->events->contains($event2));
    $this->assertFalse($customization->events->contains($event1));
  }
}
