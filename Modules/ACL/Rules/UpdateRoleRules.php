<?php

namespace Modules\ACL\Rules;

use Modules\User\Rules\StoreUserRules;

class UpdateRoleRules
{
    public static function rules()
    {
        return array_merge(StoreRoleRules::rules(), [
            'form.name' => ['nullable', 'string', 'max:255', 'unique:roles,name'],
        ]);
    }
}
