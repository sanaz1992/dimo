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
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('instagram_account_id')->constrained('instagram_accounts')->cascadeOnDelete();

            // if instagram_post_id=null => apply rule to all posts of this account
            // if instagram_post_id!=null => apply rule to this post only
            $table->foreignId('instagram_post_id')->nullable()->constrained('instagram_posts')->nullOnDelete();
            $table->string('name');
            $table->string('trigger_type', 30); // comment,message,mention,...
            $table->string('match_type', 50); // exact,contains,starts_with,ends_with
            $table->text('match_value');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('priority')->default(0); // when priority is higher, rule will be applied first
            $table->timestamps();

            $table->index(['tenant_id', 'instagram_account_id', 'is_active']);
            $table->index(['instagram_account_id', 'instagram_post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_rules');
    }
};
