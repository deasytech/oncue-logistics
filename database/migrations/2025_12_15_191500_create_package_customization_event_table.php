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
    Schema::create('package_customization_event', function (Blueprint $table) {
      $table->id();
      $table->foreignId('package_customization_id')->constrained()->onDelete('cascade');
      $table->foreignId('event_id')->constrained()->onDelete('cascade');
      $table->timestamps();

      // Add unique constraint to prevent duplicate entries with shorter name
      $table->unique(['package_customization_id', 'event_id'], 'pkg_cust_event_unique');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('package_customization_event');
  }
};
