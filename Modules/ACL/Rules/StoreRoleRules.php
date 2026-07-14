<?php

namespace Modules\ACL\Rules;

class StoreRoleRules
{
    public static function rules()
    {
        return [
            'form.name'                  => ['required', 'string', 'max:255', 'unique:roles,name'],
            'form.title'                 => ['required', 'string', 'max:255'],
            'form.selectedPermissions'   => ['array', 'required'],
            'form.selectedPermissions.*' => ['required', 'exists:permissions,name'],
        ];
    }
}
