<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
  protected $model = Customer::class;

  public function definition(): array
  {
    return [
      'title' => $this->faker->title(),
      'first_name' => $this->faker->firstName(),
      'last_name' => $this->faker->lastName(),
      'email' => $this->faker->unique()->safeEmail(),
      'phone' => $this->faker->phoneNumber(),
      'address' => $this->faker->address(),
      'state_id' => null,
      'city_id' => null,
      'setup_token' => \Illuminate\Support\Str::random(32),
      'is_active' => true,
    ];
  }
}
