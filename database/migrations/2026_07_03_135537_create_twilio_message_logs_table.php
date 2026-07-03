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
        Schema::create('twilio_message_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel'); // sms | whatsapp | whatsapp_template
            $table->string('to');
            $table->string('to_country', 8)->nullable();
            $table->string('context')->nullable(); // rsvp_invite | rsvp_reminder | test | ad_hoc
            $table->string('content_sid')->nullable();
            $table->string('message_sid')->nullable()->index();
            // queued|sent = accepted by Twilio; delivered/read/failed/undelivered arrive later via status callback
            $table->string('status');
            $table->unsignedInteger('error_code')->nullable()->index();
            $table->text('error_message')->nullable();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['channel', 'error_code']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twilio_message_logs');
    }
};
