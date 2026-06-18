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
      $table->foreignId('delivery_service_id')->nullable()->constrained()->onDelete('cascade');
      $table->decimal('cost', 10, 2)->nullable();
      $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
      $table->string('payment_method')->nullable(); // 'online', 'offline'
      $table->string('payment_reference')->nullable();
      $table->timestamp('paid_at')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('deliveries', function (Blueprint $table) {
      $table->dropForeign(['delivery_service_id']);
      $table->dropColumn([
        'delivery_service_id',
        'cost',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at'
      ]);
    });
  }
};
