<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guest>
 */
class GuestFactory extends Factory
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
      'title' => fake()->title(),
      'first_name' => fake()->firstName(),
      'last_name' => fake()->lastName(),
      'email' => fake()->unique()->safeEmail(),
      'phone' => fake()->phoneNumber(),
      // Ensure there is at least one state and city to satisfy foreign keys in tests
      'state_id' => function () {
        return \App\Models\State::firstOrCreate(['name' => 'Test State'])->id;
      },
      'city_id' => function () {
        $stateId = \App\Models\State::firstOrCreate(['name' => 'Test State'])->id;
        return \App\Models\City::firstOrCreate(['name' => 'Test City', 'state_id' => $stateId], ['state_id' => $stateId])->id;
      },
    ];
  }
}
