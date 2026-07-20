<?php

namespace Modules\ACL\External\Repositories;

use Modules\ACL\Entities\Role;
use Modules\ACL\External\Repositories\Contract\RoleRepositoryInterface;
use Modules\Core\External\Repositories\BaseRepository;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Role
    {
        return Role::create([
            'name' => str_replace(' ', '_', $data['name']),
            'title' => $data['title'],
            'guard_name' => 'web',
        ]);
    }
}
