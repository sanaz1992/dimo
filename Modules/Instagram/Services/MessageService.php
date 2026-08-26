<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Instagram\Entities\Message;
use Modules\Instagram\External\Repositories\Contract\MessageRepositoryInterface;

class MessageService
{
    public function __construct(
        protected MessageRepositoryInterface $messageRepository,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->messageRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function firstOrCreate(array $conditions, array $data)
    {
        return $this->messageRepository->firstOrCreate($conditions, $data);
    }

    public function findByColumn($col, $value)
    {
        return $this->messageRepository->findByColumn($col, $value);
    }

    public function findOrFail($id): Message
    {
        return $this->messageRepository->findOrFail($id);
    }

    public function create(array $data): Message
    {
        return DB::transaction(function () use ($data) {
            $message = $this->messageRepository->create($data);

            return $message;
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        return $this->messageRepository->updateOrCreate(
            $condition,
            $data
        );
    }

    public function update(Message $message, array $data): Message
    {
        return DB::transaction(function () use ($message, $data) {
            $message = $this->messageRepository->update($message, $data);

            return $message;
        });
    }
}
