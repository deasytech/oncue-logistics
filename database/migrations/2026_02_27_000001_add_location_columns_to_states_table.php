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
    Schema::table('states', function (Blueprint $table) {
      $table->string('iso_code', 10)->nullable()->after('name');
      $table->string('country_code', 10)->nullable()->after('iso_code');
      $table->string('latitude', 255)->nullable()->after('country_code');
      $table->string('longitude', 255)->nullable()->after('latitude');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('states', function (Blueprint $table) {
      $table->dropColumn(['iso_code', 'country_code', 'latitude', 'longitude']);
    });
  }
};
