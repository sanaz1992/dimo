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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
             $table->foreignId('order_id')->constrained()->onDelete('restrict');
            $table->string('gateway'); // zarinpal, sadad, mellat...
            $table->string('transaction_id')->nullable(); // Bank transaction number
            $table->string('reference_id')->nullable(); // Bank reference/tracking number
            $table->decimal('amount', 12, 0);
            $table->string('status'); // pending, success, failed
            $table->text('payload')->nullable(); // Full bank response log for debugging
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
