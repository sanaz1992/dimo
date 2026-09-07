<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\SyncRun;
use Modules\Core\Enums\SyncRunStatus;
use Modules\Core\Enums\SyncRunType;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Services\SyncRunService;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Instagram\Entities\InstagramPost;
use Modules\Instagram\External\Repositories\Contract\InstagramPostRepositoryInterface;
use Modules\Instagram\Jobs\SyncInstagramPosts;

class InstagramPostService
{
    public function __construct(
        protected InstagramPostRepositoryInterface $instagramPostRepository,
        protected InstagramApiService $instagramApiService,
        protected SyncRunService $syncRunService,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->instagramPostRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function firstOrCreate(array $conditions, array $data)
    {
        return $this->instagramPostRepository->firstOrCreate($conditions, $data);
    }

    public function findByColumn($col, $value)
    {
        return $this->instagramPostRepository->findByColumn($col, $value);
    }

    public function findOrFail($id): InstagramPost
    {
        return $this->instagramPostRepository->findOrFail($id);
    }

    public function create(array $data): InstagramPost
    {
        return DB::transaction(function () use ($data) {
            return $this->instagramPostRepository->create($data);
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        return $this->instagramPostRepository->updateOrCreate($condition, $data);
    }

    public function update(InstagramPost $post, array $data): InstagramPost
    {
        return DB::transaction(function () use ($post, $data) {
            return $this->instagramPostRepository->update($post, $data);
        });
    }

    public function syncPostsPage(InstagramAccount $instagramAccount, array $result): int
    {
        $posts = $result['data'] ?? [];
        foreach ($posts as $post) {
            $this->updateOrCreate(
                ['instagram_media_id' => $post['id']],
                [
                    'instagram_account_id' => $instagramAccount->id,
                    'media_product_type' => $post['media_product_type'] ?? null,
                    'caption' => $post['caption'] ?? null,
                    'permalink' => $post['permalink'] ?? null,
                    'published_at' => $post['timestamp'] ?? null,
                    'payload' => $post,
                    'comments_count' => $post['comments_count'],
                ]
            );
        }

        return count($posts);
    }

    public function startInstagramPostsSync(InstagramAccount $instagramAccount): ?SyncRun
    {
        $activeSync = $this->syncRunService
            ->getActiveRun(InstagramAccount::class, $instagramAccount->id, 'instagram_posts');
        if ($activeSync) {
            return null;
        }

        $syncRun = $this->syncRunService->create([
            'tenant_id' => $instagramAccount->tenant_id,
            'syncable_type' => InstagramAccount::class,
            'syncable_id' => $instagramAccount->id,
            'type' => SyncRunType::INSTAGRAM_POSTS,
            'status' => SyncRunStatus::PENDING,
            'processed' => 0,
        ]);

        SyncInstagramPosts::dispatch($instagramAccount->id, $syncRun->id);

        return $syncRun;
    }
}
