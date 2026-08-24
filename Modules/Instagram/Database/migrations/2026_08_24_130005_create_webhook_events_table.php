<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Instagram\Enums\WebhookEventStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('instagram_account_id')->nullable()->constrained('instagram_accounts')->nullOnDelete();
            $table->string('provider')->default('instagram');
            $table->string('event_type')->nullable();
            $table->string('event_key')->nullable();
            $table->json('payload');
            $table->string('status')->default(WebhookEventStatus::PENDING->value);
            $table->integer('attempts')->default(0);
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->unique([
                'provider',
                'event_key',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
