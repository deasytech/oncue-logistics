<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('customers', function (Blueprint $table) {
      $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
      $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('set null');
      $table->boolean('is_active')->default(true)->after('address');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('customers', function (Blueprint $table) {
      $table->dropForeign(['city_id']);
      $table->dropForeign(['state_id']);
      $table->dropColumn(['city_id', 'state_id', 'is_active']);
    });
  }
};
