<?php

namespace Database\Seeders;

use App\Models\DeliveryService;
use Illuminate\Database\Seeder;

class DeliveryServiceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $services = [
      [
        'name' => 'Only Aso Ebi & Invitation',
        'description' => 'Delivery service for Aso Ebi fabrics and invitation cards only',
        'cost' => 15000.00,
        'is_active' => true,
        'applicable_to' => 'both',
      ],
      [
        'name' => 'Aso Ebi, Invitation & Packaging',
        'description' => 'Complete delivery service including Aso Ebi, invitations, and packaging materials',
        'cost' => 25000.00,
        'is_active' => true,
        'applicable_to' => 'both',
      ],
      [
        'name' => 'Invitation Card (Apply to Corporate only)',
        'description' => 'Invitation card delivery service specifically for corporate events',
        'cost' => 8000.00,
        'is_active' => true,
        'applicable_to' => 'corporate',
      ],
      [
        'name' => 'Packages Only',
        'description' => 'Delivery service for event packages and materials',
        'cost' => 12000.00,
        'is_active' => true,
        'applicable_to' => 'both',
      ],
    ];

    foreach ($services as $service) {
      DeliveryService::create($service);
    }
  }
}
