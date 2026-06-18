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
        Schema::table('guest_fabric_selections', function (Blueprint $table) {
            // Add delivery_zone_id column
            $table->unsignedBigInteger('delivery_zone_id')->nullable()->after('delivery_service_id');

            // Remove the old delivery_service_id foreign key constraint
            $table->dropForeign(['delivery_service_id']);

            // Drop the old delivery_service_id column
            $table->dropColumn('delivery_service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_fabric_selections', function (Blueprint $table) {
            // Add back the delivery_service_id column
            $table->unsignedBigInteger('delivery_service_id')->nullable()->after('delivery_zone_id');

            // Add foreign key constraint back
            $table->foreign('delivery_service_id')->references('id')->on('delivery_services');

            // Drop the delivery_zone_id column
            $table->dropColumn('delivery_zone_id');
        });
    }
};
