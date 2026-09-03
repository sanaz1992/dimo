<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstagramComment;
use Modules\Instagram\Services\AutomationService;

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
        AutomationService $automationService
    ): void {
        $comment = InstagramComment::query()
            ->with('instagramAccount')
            ->find($this->commentId);

        if (! $comment) {
            Log::warning(
                'Instagram comment not found for automation',
                [
                    'comment_id' => $this->commentId,
                ]
            );

            return;
        }

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
