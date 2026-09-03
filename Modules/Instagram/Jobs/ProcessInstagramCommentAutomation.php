<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Services\AutomationService;
use Modules\Instagram\Services\InstagramCommentService;

class ProcessInstagramCommentAutomation implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public int $commentId
    ) {}

    public function handle(
        AutomationService $automationService,
        InstagramCommentService $commentService
    ): void {
        $comment = $commentService->findByColumn('id', $this->commentId);
        if (! $comment) {
            Log::warning(
                'Instagram comment not found for automation',
                ['comment_id' => $this->commentId]
            );

            return;
        }
        $comment->load('instagramAccount');
        $automationService->processComment($comment);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error(
            '=== Instagram comment automation job failed ===',
            [
                'comment_id' => $this->commentId,
                'error' => $exception->getMessage(),
            ]
        );
    }
}
