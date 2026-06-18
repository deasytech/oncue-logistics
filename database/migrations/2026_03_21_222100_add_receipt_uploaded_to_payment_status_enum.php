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
    Schema::table('deliveries', function (Blueprint $table) {
      // Drop the existing enum and recreate it with the new value
      $table->enum('payment_status', ['pending', 'paid', 'failed', 'receipt_uploaded'])
        ->default('pending')
        ->change();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('deliveries', function (Blueprint $table) {
      // Drop the existing enum and recreate it without the new value
      $table->enum('payment_status', ['pending', 'paid', 'failed'])
        ->default('pending')
        ->change();
    });
  }
};
