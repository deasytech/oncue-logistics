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
        Schema::create('package_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('package_materials')->nullOnDelete();
            $table->foreignId('font_id')->nullable()->constrained('package_fonts')->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('package_colors')->nullOnDelete();
            $table->string('message')->nullable();
            $table->string('location')->nullable(); // delivery/pickup note
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0); // computed
            $table->decimal('total_price', 12, 2)->default(0); // quantity * unit_price
            $table->string('preview_image_path')->nullable(); // generated preview or snapshot
            $table->json('meta')->nullable(); // e.g. {"3d_model":"...","font_size":36}
            $table->enum('status', ['draft', 'in_cart', 'ordered', 'paid', 'cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_customizations');
    }
};
