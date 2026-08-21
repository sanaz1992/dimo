<?php

namespace Modules\Tenant\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Enums\TenantStatus;
use Modules\Tenant\External\Repositories\Contract\TenantRepositoryInterface;

class TenantService
{
    public function __construct(
        protected TenantRepositoryInterface $tenantRepository,
    ) {
    }

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->tenantRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function findByColumn($col, $value)
    {
        return $this->tenantRepository->findByColumn($col, $value);
    }

    public function create(array $data): Tenant
    {
        $data['status'] = $data['status'] ?? TenantStatus::ACTIVE->value;

        return DB::transaction(function () use ($data) {
            $tenant = $this->tenantRepository->create($data);

            return $tenant;
        });
    }

    public function update(Tenant $tenant, array $data): Tenant
    {
        return DB::transaction(function () use ($tenant, $data) {
            $tenant = $this->tenantRepository->update($tenant, $data);

            return $tenant;
        });
    }

    public function delete(Tenant $tenant): bool
    {
        return $this->tenantRepository->delete($tenant->id);
    }

    public function restore($slug)
    {
        return $this->tenantRepository->restore($slug);
    }
}
