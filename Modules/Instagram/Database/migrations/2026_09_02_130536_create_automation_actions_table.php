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
        Schema::create('automation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_rule_id')->constrained('automation_rules')->cascadeOnDelete();
            $table->string('action_type', 30); // send_message,add_tag,send_email,...
            $table->unsignedInteger('sort_order')->default(0); // sort for execution order of actions
            $table->json('config'); // action settings. for example, for send_message action, it can be like:
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['automation_rule_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_actions');
    }
};
