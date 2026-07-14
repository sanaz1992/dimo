<?php

namespace Modules\ACL\External\Repositories;

use Modules\ACL\Entities\Permission;
use Modules\ACL\External\Repositories\Contract\PermissionRepositoryInterface;
use Modules\Core\External\Repositories\BaseRepository;


class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }
}
