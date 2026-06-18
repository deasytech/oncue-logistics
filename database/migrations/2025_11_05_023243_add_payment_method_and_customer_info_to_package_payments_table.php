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
        Schema::table('package_payments', function (Blueprint $table) {
            $table->string('payment_method')->default('online')->after('amount');
            $table->json('customer_info')->nullable()->after('payment_method');
            $table->text('payment_notes')->nullable()->after('customer_info');
            $table->timestamp('paid_at')->nullable()->after('payment_notes');
            $table->string('payment_proof')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'customer_info',
                'payment_notes',
                'paid_at',
                'payment_proof'
            ]);
        });
    }
};
