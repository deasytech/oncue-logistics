<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categories = [
      'Social' => [
        'White Wedding',
        'Traditional Engagement',
        'Introduction',
        'Birthday',
        'Anniversary',
        'Funerals',
      ],
      'Corporate' => [
        'Anniversary',
      ],
      'Order' => [
        'Gender Review',
      ],
      'Custom' => [
        'Other',
      ],
    ];

    foreach ($categories as $parentName => $children) {
      // Insert parent
      $parentId = DB::table('categories')->insertGetId([
        'name'       => $parentName,
        'parent_id'  => null,
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      // Insert children
      foreach ($children as $childName) {
        DB::table('categories')->insert([
          'name'       => $childName,
          'parent_id'  => $parentId,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }
    }
  }
}
