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
        Schema::table('twilio_message_logs', function (Blueprint $table) {
            // Enough of the original send call to replay it verbatim from the retry action
            // (template variables for whatsapp_template, message body for sms/whatsapp).
            $table->json('payload')->nullable()->after('error_message');
            $table->timestamp('retried_at')->nullable()->after('payload');
            $table->foreignId('retry_of_id')->nullable()->after('retried_at')
                ->constrained('twilio_message_logs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('twilio_message_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('retry_of_id');
            $table->dropColumn(['payload', 'retried_at']);
        });
    }
};
