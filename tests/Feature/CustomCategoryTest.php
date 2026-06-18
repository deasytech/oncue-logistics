<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomCategoryTest extends TestCase
{
  use RefreshDatabase;

  public function test_event_can_have_custom_subcategory()
  {
    // Create the Custom category and its subcategory
    $customCategory = Category::create([
      'name' => 'Custom',
      'parent_id' => null,
      'is_active' => true,
    ]);

    $otherSubcategory = Category::create([
      'name' => 'Other',
      'parent_id' => $customCategory->id,
      'is_active' => true,
    ]);

    $customer = Customer::factory()->create();

    $event = Event::create([
      'name' => 'Test Custom Event',
      'customer_id' => $customer->id,
      'category_id' => $customCategory->id,
      'subcategory_id' => $otherSubcategory->id,
      'custom_subcategory' => 'My Custom Event Type',
      'location' => 'Test Location',
      'estimated_number_of_guest' => 100,
      'event_date' => now()->addDays(30),
      'aso_ebi_color' => 'Blue',
      'is_active' => true,
    ]);

    $this->assertEquals($customCategory->id, $event->category_id);
    $this->assertEquals('My Custom Event Type', $event->custom_subcategory);
    $this->assertEquals('My Custom Event Type', $event->display_subcategory);
  }

  public function test_event_with_regular_category_works_as_before()
  {
    // Create regular categories
    $socialCategory = Category::create([
      'name' => 'Social',
      'parent_id' => null,
      'is_active' => true,
    ]);

    $weddingSubcategory = Category::create([
      'name' => 'White Wedding',
      'parent_id' => $socialCategory->id,
      'is_active' => true,
    ]);

    $customer = Customer::factory()->create();

    $event = Event::create([
      'name' => 'Test Regular Event',
      'customer_id' => $customer->id,
      'category_id' => $socialCategory->id,
      'subcategory_id' => $weddingSubcategory->id,
      'location' => 'Test Location',
      'estimated_number_of_guest' => 100,
      'event_date' => now()->addDays(30),
      'aso_ebi_color' => 'Red',
      'is_active' => true,
    ]);

    $this->assertEquals($socialCategory->id, $event->category_id);
    $this->assertEquals($weddingSubcategory->id, $event->subcategory_id);
    $this->assertNull($event->custom_subcategory);
    $this->assertEquals('White Wedding', $event->display_subcategory);
  }
}
