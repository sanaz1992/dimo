<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Instagram\Entities\WebhookEvent;
use Modules\Instagram\External\Repositories\Contract\WebhookEventRepositoryInterface;

class WebhookEventService
{
    public function __construct(
        protected WebhookEventRepositoryInterface $webhookEventRepository,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->webhookEventRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function findByColumn($col, $value)
    {
        return $this->webhookEventRepository->findByColumn($col, $value);
    }

    public function findOrFail($id): WebhookEvent
    {
        return $this->webhookEventRepository->findOrFail($id);
    }

    public function create(array $data): WebhookEvent
    {
        return DB::transaction(function () use ($data) {
            $webhookEvent = $this->webhookEventRepository->create($data);

            return $webhookEvent;
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        return $this->webhookEventRepository->updateOrCreate(
            $condition,
            $data
        );
    }

    public function update(WebhookEvent $webhookEvent, array $data): WebhookEvent
    {
        return DB::transaction(function () use ($webhookEvent, $data) {
            $webhookEvent = $this->webhookEventRepository->update($webhookEvent, $data);

            return $webhookEvent;
        });
    }
}
