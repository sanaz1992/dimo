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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_sku_id')->constrained()->onDelete('restrict');

            $table->unsignedInteger('quantity');
            $table->decimal('price', 12, 0); // Unit price at the time of purchase
            $table->decimal('discount', 12, 0)->default(0); // Discount applied to this item
            $table->decimal('total', 12, 0); // Total for this line item
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
