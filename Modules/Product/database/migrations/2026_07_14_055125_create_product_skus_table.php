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
        Schema::create('product_skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique(); // Unique product code
            $table->string('packaging_type'); // PET, Glass, Gallon
            $table->unsignedInteger('volume_ml'); // Volume in milliliters (e.g., 1000 for one liter)
            $table->decimal('price', 12, 0); // Price in Rial/Toman
            $table->decimal('sale_price', 12, 0)->nullable(); // Discounted price
            $table->unsignedInteger('stock')->default(0); // Total available stock for online sales
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_skus');
    }
};
