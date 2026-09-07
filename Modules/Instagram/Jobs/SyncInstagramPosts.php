<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Core\Enums\SyncRunStatus;
use Modules\Core\Services\SyncRunService;
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

    public function __construct(
        public int $instagramAccountId,
        public int $syncRunId,
        public ?string $after = null,
    ) {}

    public function handle(
        InstagramAccountService $instagramAccountService,
        InstagramApiService $instagramApiService,
        InstagramPostService $instagramPostService,
        SyncRunService $syncRunService,
    ): void {

        $instagramAccount = $instagramAccountService->findByColumn('id', $this->instagramAccountId);
        if (! $instagramAccount) {
            Log::warning(
                'Instagram account not found for posts sync.',
                [
                    'instagram_account_id' => $this->instagramAccountId,
                    'sync_run_id' => $this->syncRunId,
                ]
            );

            return;
        }

        $syncRun = $syncRunService->findOrFail($this->syncRunId);

        /*
         * فقط Job اول Sync را از pending به running می‌برد.
         */
        if ($syncRun->status === SyncRunStatus::PENDING) {
            $syncRunService->start($syncRun);
        }

        try {
            $result = $instagramApiService->getPosts($instagramAccount, 50, $this->after);

            $count = $instagramPostService->syncPostsPage($instagramAccount, $result);

            $syncRunService->incrementProcessed($syncRun, $count);

            $nextAfter = $result['paging']['cursors']['after'] ?? null;

            Log::info(
                'Instagram posts page synced.',
                [
                    'instagram_account_id' => $instagramAccount->id,
                    'sync_run_id' => $syncRun->id,
                    'count' => $count,
                    'after' => $this->after,
                    'has_next_page' => (bool) $nextAfter,
                ]
            );

            if ($nextAfter) {
                self::dispatch($instagramAccount->id, $syncRun->id, $nextAfter);

                return;
            }

            /*
             * این آخرین صفحه بوده.
             */
            $syncRunService->complete($syncRun);

            Log::info(
                'Instagram posts sync completed.',
                [
                    'instagram_account_id' => $instagramAccount->id,
                    'sync_run_id' => $syncRun->id,
                ]
            );
        } catch (\Throwable $exception) {
            Log::warning(
                'Instagram posts sync attempt failed.',
                [
                    'instagram_account_id' => $instagramAccount->id,
                    'sync_run_id' => $syncRun->id,
                    'after' => $this->after,
                    'error' => $exception->getMessage(),
                ]
            );

            throw $exception;
        }
    }

    public function failed(\Throwable $exception): void
    {
        try {
            $syncRunService = app(SyncRunService::class);

            $syncRun = $syncRunService->findOrFail($this->syncRunId);

            $syncRunService->fail($syncRun, $exception->getMessage());
        } catch (\Throwable $e) {
            Log::error(
                'Failed to update sync run after job failure.',
                [
                    'sync_run_id' => $this->syncRunId,
                    'error' => $e->getMessage(),
                ]
            );
        }

        Log::error(
            'Instagram posts sync job failed.',
            [
                'instagram_account_id' => $this->instagramAccountId,
                'sync_run_id' => $this->syncRunId,
                'after' => $this->after,
                'error' => $exception->getMessage(),
            ]
        );
    }
}
