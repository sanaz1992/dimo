<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Instagram\Enums\InstagramAccountStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instagram_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('facebook_page_id')->nullable();
            $table->string('instagram_account_id')->unique(); // شناسه IG از متا
            $table->string('username', 50)->nullable();
            $table->string('name', 50)->nullable();
            $table->text('profile_picture_url')->nullable();
            $table->text('access_token'); // برای ذخیره با cast encrypted
            $table->timestamp('token_expires_at')->nullable();
            $table->json('scopes')->nullable();
            $table->string('status', 30)->default(InstagramAccountStatus::CONNECTED->value); // , ['connected', 'expired', 'revoked', 'error']
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_accounts');
    }
};
