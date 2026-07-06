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
    Schema::table('events', function (Blueprint $table) {
      $table->timestamp('rsvp_expires_at')
        ->nullable()
        ->after('is_active')
        ->comment('Optional RSVP deadline for this event. When set, all new guest RSVPs inherit this expiry. NULL means RSVPs never expire.');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('events', function (Blueprint $table) {
      $table->dropColumn('rsvp_expires_at');
    });
  }
};
