<?php

namespace Modules\ACL\Entities;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['title', 'name', 'guard_name'];
}
