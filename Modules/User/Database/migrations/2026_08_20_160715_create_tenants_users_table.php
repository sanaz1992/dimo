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
        Schema::create('tenants_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constracts('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->constracts('tenants')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
        });
        Schema::dropIfExists('tenants_users');
    }
};
