<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Services\InstagramAccountService;
use Modules\Instagram\Services\InstagramApiService;
use Modules\Instagram\Services\InstagramPostService;

class SyncInstagramPosts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(public int $instagramAccountId, public ?string $after = null) {}

    public function handle(
        InstagramAccountService $instagramAccountService,
        InstagramApiService $instagramApiService,
        InstagramPostService $instagramPostService,
    ): void {
        $instagramAccount = $instagramAccountService->findByColumn('id', $this->instagramAccountId);

        if (! $instagramAccount) {
            Log::warning(
                'Instagram account not found for posts sync.',
                [
                    'instagram_account_id' => $this->instagramAccountId,
                ]
            );

            return;
        }

        $result = $instagramApiService->getPosts($instagramAccount, 50, $this->after);

        $count = $instagramPostService->syncPostsPage($instagramAccount, $result);

        $nextAfter = $result['paging']['cursors']['after'] ?? null;

        Log::info(
            'Instagram posts page synced.',
            [
                'instagram_account_id' => $instagramAccount->id,
                'count' => $count,
                'has_next_page' => (bool) $nextAfter,
            ]
        );

        if ($nextAfter) {
            self::dispatch($instagramAccount->id, $nextAfter);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error(
            'Instagram posts sync job failed.',
            [
                'instagram_account_id' => $this->instagramAccountId,
                'after' => $this->after,
                'error' => $exception->getMessage(),
            ]
        );
    }
}
