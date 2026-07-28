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
        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_sku_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity');
            $table->string('status')->default('active'); // active, released, converted, expired
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['product_sku_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_reservations');
    }
};
