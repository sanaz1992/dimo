<?php

namespace Modules\Tenant\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Enums\TenantStatus;
use Modules\Tenant\External\Repositories\Contract\TenantRepositoryInterface;
use Modules\User\Services\UserService;

class TenantService
{
    public function __construct(
        protected TenantRepositoryInterface $tenantRepository,
    ) {}

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
        $user = app(UserService::class)->findByColumn('unique_code', $data['user']);

        return DB::transaction(function () use ($data, $user) {
            $tenant = $this->tenantRepository->create($data);
            $tenant->users()->attach($user->id);

            return $tenant;
        });
    }

    public function update(Tenant $tenant, array $data): Tenant
    {
        return DB::transaction(function () use ($tenant, $data) {
            $tenant = $this->tenantRepository->update($tenant, $data);
            if (isset($data['user']) && $data['user'] != '') {
                $user = app(UserService::class)->findByColumn('unique_code', $data['user']);
                $tenant->users()->sync($user->id);
            }

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
