<?php

namespace Modules\ACL\Services;

use Modules\User\Entities\User;

class UserRoleService
{
    public function updateUserRoles(User $user, array $data): User
    {
        $user->syncRoles($data['selectedRoles']);
        $user->syncPermissions($data['selectedPermissions']);
        return $user;
    }
}
