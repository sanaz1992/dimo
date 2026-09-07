<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\DB;
use Modules\Instagram\Entities\AutomationActionRun;
use Modules\Instagram\Enums\AutomationActionRunStatus;
use Modules\Instagram\External\Repositories\Contract\AutomationActionRunRepositoryInterface;

class AutomationActionRunService
{
    public function __construct(
        protected AutomationActionRunRepositoryInterface $automationActionRunRepository,
    ) {}

    public function firstOrCreate(array $conditions, array $data)
    {
        return $this->automationActionRunRepository->firstOrCreate($conditions, $data);
    }

    public function create(array $data): AutomationActionRun
    {
        return DB::transaction(function () use ($data) {
            return $this->automationActionRunRepository->create($data);
        });
    }

    public function update(AutomationActionRun $automationActionRun, array $data): AutomationActionRun
    {
        return DB::transaction(function () use ($automationActionRun, $data) {
            $automationActionRun->update($data);

            return $automationActionRun->refresh();
        });
    }

    public function start(AutomationActionRun $actionRun): AutomationActionRun
    {
        return $this->update($actionRun, [
            'status' => AutomationActionRunStatus::PROCESSING,
            'started_at' => now(),
            'completed_at' => null,
            'error' => null,
        ]);
    }

    public function complete(AutomationActionRun $actionRun, ?array $context = null): AutomationActionRun
    {
        $data = [
            'status' => AutomationActionRunStatus::COMPLETED,
            'completed_at' => now(),
            'error' => null,
        ];

        if ($context !== null) {
            $data['context'] = $context;
        }

        return $this->update($actionRun, $data);
    }

    public function fail(AutomationActionRun $actionRun, string $error): AutomationActionRun
    {
        return $this->update($actionRun, [
            'status' => AutomationActionRunStatus::FAILED,
            'error' => $error,
            'completed_at' => now(),
        ]);
    }

    public function skip(AutomationActionRun $actionRun): AutomationActionRun
    {
        return $this->update($actionRun, [
            'status' => AutomationActionRunStatus::SKIPPED,
            'completed_at' => now(),
        ]);
    }
}
