<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Instagram\Entities\AutomationRule;
use Modules\Instagram\External\Repositories\Contract\AutomationRuleRepositoryInterface;

class AutomationRuleService
{
    public function __construct(
        protected AutomationRuleRepositoryInterface $AutomationRuleRepository
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->AutomationRuleRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function firstOrCreate(array $conditions, array $data)
    {
        return $this->AutomationRuleRepository->firstOrCreate($conditions, $data);
    }

    public function create(array $data): AutomationRule
    {
        return DB::transaction(function () use ($data) {
            $automationRule = $this->AutomationRuleRepository->create($data);

            return $automationRule;
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        return $this->AutomationRuleRepository->updateOrCreate($condition, $data);
    }

    public function update(AutomationRule $automationRule, array $data): AutomationRule
    {
        return DB::transaction(function () use ($automationRule, $data) {
            $automationRule = $this->AutomationRuleRepository->update($automationRule, $data);

            return $automationRule;
        });
    }
}
