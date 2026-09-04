<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Instagram\Entities\AutomationAction;
use Modules\Instagram\External\Repositories\Contract\AutomationActionRepositoryInterface;

class AutomationActionService
{
    public function __construct(
        protected AutomationActionRepositoryInterface $automationActionRepository
    ) {}

    public function findByColumn($col, $value)
    {
        return $this->automationActionRepository->findByColumn($col, $value);
    }

    public function create(array $data): AutomationAction
    {
        return DB::transaction(function () use ($data) {
            $automationRule = $this->automationActionRepository->create($data);

            return $automationRule;
        });
    }

    public function delete(AutomationAction $automationAction): bool
    {
        return $this->automationActionRepository->delete($automationAction->id);
    }
}
