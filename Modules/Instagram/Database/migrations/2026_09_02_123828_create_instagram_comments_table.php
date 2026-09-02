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
        Schema::create('instagram_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instagram_post_id')->constrained('instagram_posts')->cascadeOnDelete();
            $table->foreignId('instagram_account_id')->constrained('instagram_accounts')->cascadeOnDelete();
            $table->string('instagram_comment_id')->unique(); // comment id in instagram
            $table->string('commenter_ig_id'); // ig id for writer comment
            $table->string('commenter_username')->nullable(); // username for writer comment
            $table->text('comment_text')->nullable();
            $table->timestamp('commented_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index('commenter_ig_id');
            $table->index('instagram_post_id');
            $table->index('instagram_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_comments');
    }
};
