<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Instagram\Enums\AutomationActionRunStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('automation_action_runs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('automation_run_id')->constrained('automation_runs')->cascadeOnDelete();
            $table->foreignId('automation_action_id')->constrained('automation_actions')->cascadeOnDelete();
            $table->string('status', 20)->default(AutomationActionRunStatus::PENDING->value);
            $table->text('error')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['automation_run_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_action_runs');
    }
};
