<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Instagram\Entities\AutomationRun;
use Modules\Instagram\External\Repositories\Contract\AutomationRunRepositoryInterface;

class AutomationRunService
{
    public function __construct(
        protected AutomationRunRepositoryInterface $automationRunRepository
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->automationRunRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function firstOrCreate(array $conditions, array $data)
    {
        return $this->automationRunRepository->firstOrCreate($conditions, $data);
    }

    public function create(array $data): AutomationRun
    {
        return DB::transaction(function () use ($data) {
            $automationRun = $this->automationRunRepository->create($data);

            return $automationRun;
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        return $this->automationRunRepository->updateOrCreate($condition, $data);
    }

    public function update(AutomationRun $automationRun, array $data): AutomationRun
    {
        return DB::transaction(function () use ($automationRun, $data) {
            $automationRun = $this->automationRunRepository->update($automationRun, $data);

            return $automationRun;
        });
    }
}
