<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Instagram\Entities\AutomationRun;
use Modules\Instagram\External\Repositories\Contract\AutomationRunRepositoryInterface;

class AutomationRunService
{
    public function __construct(
        protected AutomationRunRepositoryInterface $AutomationRunRepository
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->AutomationRunRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function firstOrCreate(array $conditions, array $data)
    {
        return $this->AutomationRunRepository->firstOrCreate($conditions, $data);
    }

    public function create(array $data): AutomationRun
    {
        return DB::transaction(function () use ($data) {
            $automationRun = $this->AutomationRunRepository->create($data);

            return $automationRun;
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        return $this->AutomationRunRepository->updateOrCreate($condition, $data);
    }

    public function update(AutomationRun $automationRun, array $data): AutomationRun
    {
        return DB::transaction(function () use ($automationRun, $data) {
            $automationRun = $this->AutomationRunRepository->update($automationRun, $data);

            return $automationRun;
        });
    }
}
