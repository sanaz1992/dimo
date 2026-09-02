<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Instagram\Entities\InstagramPost;
use Modules\Instagram\External\Repositories\Contract\InstagramPostRepositoryInterface;

class InstagramPostService
{
    public function __construct(
        protected InstagramPostRepositoryInterface $instagramPostRepository,
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
            $post = $this->instagramPostRepository->create($data);

            return $post;
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        return $this->instagramPostRepository->updateOrCreate(
            $condition,
            $data
        );
    }

    public function update(InstagramPost $post, array $data): InstagramPost
    {
        return DB::transaction(function () use ($post, $data) {
            $post = $this->instagramPostRepository->update($post, $data);

            return $post;
        });
    }
}
