<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'customer_id' => Customer::factory(),
      'category_id' => 1, // Default category
      'name' => fake()->words(3, true),
      'event_date' => fake()->dateTimeBetween('now', '+6 months'),
      'location' => fake()->address(),
      'estimated_number_of_guest' => fake()->numberBetween(50, 500),
      'aso_ebi_color' => fake()->safeColorName(),
      'notes' => fake()->paragraph(),
      'is_active' => true,
    ];
  }
}
