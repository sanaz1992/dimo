<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\SyncRun;
use Modules\Core\Enums\SyncRunStatus;
use Modules\Core\External\Repositories\Contract\SyncRunRepositoryInterface;

class SyncRunService
{
    public function __construct(
        protected SyncRunRepositoryInterface $syncRunRepository
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], $filter = null)
    {
        return $this->syncRunRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function getActiveRun(string $syncableType, int $syncableId, string $type): ?SyncRun
    {
        return SyncRun::query()
            ->where('syncable_type', $syncableType)
            ->where('syncable_id', $syncableId)
            ->where('type', $type)
            ->whereIn('status', [SyncRunStatus::PENDING, SyncRunStatus::RUNNING])
            ->latest()
            ->first();
    }

    public function findByColumn(string $column, mixed $value)
    {
        return $this->syncRunRepository->findByColumn($column, $value);
    }

    public function findOrFail(int $id): SyncRun
    {
        return $this->syncRunRepository->findOrFail($id);
    }

    public function create(array $data): SyncRun
    {
        return DB::transaction(
            function () use ($data) {
                return $this->syncRunRepository->create($data);
            }
        );
    }

    public function update(SyncRun $syncRun, array $data): SyncRun
    {
        return DB::transaction(
            function () use ($syncRun, $data) {
                return $this->syncRunRepository->update($syncRun, $data);
            }
        );
    }

    public function firstOrCreate(array $conditions, array $data): SyncRun
    {
        return $this->syncRunRepository->firstOrCreate($conditions, $data);
    }

    public function updateOrCreate(array $conditions, array $data): SyncRun
    {
        return $this->syncRunRepository->updateOrCreate($conditions, $data);
    }

    public function start(SyncRun $syncRun): SyncRun
    {
        return $this->update(
            $syncRun,
            [
                'status' => SyncRunStatus::RUNNING,
                'started_at' => now(),
                'completed_at' => null,
                'failed_at' => null,
                'error' => null,
            ]
        );
    }

    public function complete(SyncRun $syncRun): SyncRun
    {
        return $this->update(
            $syncRun,
            [
                'status' => SyncRunStatus::COMPLETED,
                'completed_at' => now(),
                'failed_at' => null,
                'error' => null,
            ]
        );
    }

    public function fail(SyncRun $syncRun, string $error): SyncRun
    {
        return $this->update(
            $syncRun,
            [
                'status' => SyncRunStatus::FAILED,
                'failed_at' => now(),
                'error' => $error,
            ]
        );
    }

    public function cancel(SyncRun $syncRun): SyncRun
    {
        return $this->update($syncRun, ['status' => SyncRunStatus::CANCELLED]);
    }

    public function incrementProcessed(SyncRun $syncRun, int $amount = 1): SyncRun
    {
        $syncRun->increment('processed', $amount);

        return $syncRun->refresh();
    }

    public function updateProgress(SyncRun $syncRun, int $processed, ?int $total = null): SyncRun
    {
        $data = ['processed' => $processed];
        if ($total !== null) {
            $data['total'] = $total;
        }

        return $this->update($syncRun, $data);
    }

    public function getLatestRun(string $syncableType, int $syncableId, string $type): ?SyncRun
    {
        return SyncRun::query()
            ->where('syncable_type', $syncableType)
            ->where('syncable_id', $syncableId)
            ->where('type', $type)
            ->latest()
            ->first();
    }

    public function getLatestRunForTenant(array $tenantId, string $type): ?SyncRun
    {
        return SyncRun::query()
            ->whereIn('tenant_id', $tenantId)
            ->where('type', $type)
            ->latest()
            ->first();
    }

    public function getActiveRunsForTenants(array $tenantId, string $type)
    {
        return SyncRun::query()
            ->whereIn('tenant_id', $tenantId)
            ->where('type', $type)
            ->whereIn('status', [SyncRunStatus::PENDING, SyncRunStatus::RUNNING])
            ->latest()
            ->get();
    }
}
