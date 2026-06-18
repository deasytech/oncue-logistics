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
        Schema::create('event_guest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();

            // RSVP / invitation info
            $table->string('attendance_status')->nullable(); // e.g. invited, confirmed, attended, declined
            $table->boolean('plus_one')->default(false);
            $table->string('rsvp_token')->nullable()->unique();
            $table->unsignedInteger('reminder_attempts')->default(0);

            $table->timestamp('rsvp_sent_at')->nullable();
            $table->timestamp('rsvp_expires_at')->nullable();
            $table->timestamp('rsvp_responded_at')->nullable();
            $table->timestamp('last_reminder_sent_at')->nullable();

            $table->timestamps();

            $table->unique(['event_id', 'guest_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_guest');
    }
};
