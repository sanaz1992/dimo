<?php

namespace Modules\ACL\Services;

use Modules\ACL\External\Repositories\Contract\PermissionRepositoryInterface;

class PermissionService
{

    public function __construct(protected PermissionRepositoryInterface $permissionRepository) {}

    public function list(string $orderBy = null, array $limit = [], array $with = [], array $conditions = [])
    {
        return $this->permissionRepository->all($orderBy, $limit, $with, $conditions);
    }
}
