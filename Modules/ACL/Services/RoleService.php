<?php

namespace Modules\ACL\Services;

use Illuminate\Support\Facades\DB;
use Modules\ACL\Entities\Role;
use Modules\ACL\External\Repositories\Contract\RoleRepositoryInterface;

class RoleService
{
    public function __construct(protected RoleRepositoryInterface $roleRepository) {}

    public function list(string $orderBy = null, array $limit = [], array $with = [], array $conditions = [])
    {
        return $this->roleRepository->all($orderBy, $limit, $with, $conditions);
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = $this->roleRepository->create($data);
            $role->syncPermissions($data['selectedPermissions']);
            return $role;
        });
    }

    public function update(Role $role, array $data)
    {
        return DB::transaction(function () use ($data, $role) {
            $this->roleRepository->update($role, $data);
            $role->syncPermissions($data['selectedPermissions']);
            return $role;
        });
    }
}
