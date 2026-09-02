<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Instagram\Entities\InstagramComment;
use Modules\Instagram\External\Repositories\Contract\InstagramCommentRepositoryInterface;

class InstagramCommentService
{
    public function __construct(
        protected InstagramCommentRepositoryInterface $instagramCommentRepository,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->instagramCommentRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function firstOrCreate(array $conditions, array $data)
    {
        return $this->instagramCommentRepository->firstOrCreate($conditions, $data);
    }

    public function findByColumn($col, $value)
    {
        return $this->instagramCommentRepository->findByColumn($col, $value);
    }

    public function findOrFail($id): InstagramComment
    {
        return $this->instagramCommentRepository->findOrFail($id);
    }

    public function create(array $data): InstagramComment
    {
        return DB::transaction(function () use ($data) {
            $comment = $this->instagramCommentRepository->create($data);

            return $comment;
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        return $this->instagramCommentRepository->updateOrCreate(
            $condition,
            $data
        );
    }

    public function update(InstagramComment $comment, array $data): InstagramComment
    {
        return DB::transaction(function () use ($comment, $data) {
            $comment = $this->instagramCommentRepository->update($comment, $data);

            return $comment;
        });
    }
}
