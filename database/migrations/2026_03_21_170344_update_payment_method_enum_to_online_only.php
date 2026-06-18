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
        // Update guest_fabric_selections table
        Schema::table('guest_fabric_selections', function (Blueprint $table) {
            $table->enum('payment_method', ['online'])->default('online')->change();
        });

        // Update guest_package_selections table
        Schema::table('guest_package_selections', function (Blueprint $table) {
            $table->enum('payment_method', ['online'])->default('online')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert guest_fabric_selections table
        Schema::table('guest_fabric_selections', function (Blueprint $table) {
            $table->enum('payment_method', ['online', 'offline'])->default('offline')->change();
        });

        // Revert guest_package_selections table
        Schema::table('guest_package_selections', function (Blueprint $table) {
            $table->enum('payment_method', ['online', 'offline'])->default('offline')->change();
        });
    }
};
