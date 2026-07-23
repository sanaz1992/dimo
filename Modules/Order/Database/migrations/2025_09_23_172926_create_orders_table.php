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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('set null');
            $table->string('order_number')->unique(); // Unique order number (e.g., G-10023)

            // Statuses: custom enum or string
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, failed, refunded

            $table->decimal('subtotal', 12, 0)->nullable(); // Total price before discount and shipping cost
            $table->decimal('discount_amount', 12, 0)->default(0);
            $table->decimal('shipping_cost', 12, 0)->default(0);
            $table->decimal('total_amount', 12, 0)->nullable(); // Final payable amount

            $table->text('notes')->nullable(); // Customer notes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
