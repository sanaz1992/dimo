<?php

namespace Modules\ACL\Entities;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['title', 'name', 'guard_name'];

    public function getRouteKeyName(): string
    {
        return 'name';
    }
}
