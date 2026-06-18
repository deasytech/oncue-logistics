<?php

namespace Database\Seeders;

use App\Models\FabricType;
use Illuminate\Database\Seeder;

class FabricTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $fabricTypes = [
      [
        'name' => 'Engagement Fabric',
        'description' => 'Fabric specifically for engagement ceremonies',
        'base_price' => 1500.00,
        'is_active' => true,
      ],
      [
        'name' => 'Wedding Fabric',
        'description' => 'Fabric specifically for wedding ceremonies',
        'base_price' => 2000.00,
        'is_active' => true,
      ],
      [
        'name' => 'Wedding & Engagement Fabric',
        'description' => 'Combined fabric for both wedding and engagement ceremonies',
        'base_price' => 3000.00,
        'is_active' => true,
      ],
      [
        'name' => 'Gele only',
        'description' => 'Head tie fabric only',
        'base_price' => 500.00,
        'is_active' => true,
      ],
      [
        'name' => 'Fila Only',
        'description' => 'Cap fabric only',
        'base_price' => 400.00,
        'is_active' => true,
      ],
      [
        'name' => 'Gele & Fila',
        'description' => 'Combined head tie and cap fabric',
        'base_price' => 800.00,
        'is_active' => true,
      ],
      [
        'name' => 'Invitation Card only',
        'description' => 'Invitation cards only',
        'base_price' => 200.00,
        'is_active' => true,
      ],
    ];

    foreach ($fabricTypes as $fabricType) {
      FabricType::firstOrCreate(
        ['name' => $fabricType['name']],
        $fabricType
      );
    }
  }
}
