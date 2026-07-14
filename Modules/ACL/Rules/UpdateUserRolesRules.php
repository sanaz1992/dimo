<?php

namespace Modules\ACL\Rules;

class UpdateUserRolesRules
{
    public static function rules(): array
    {
        return [
            'form.selectedRoles'         => ['nullable', 'array'],
            'form.selectedRoles.*'       => ['nullable', 'exists:roles,name'],
            'form.selectedPermissions'   => ['nullable', 'array'],
            'form.selectedPermissions.*' => ['nullable', 'exists:permissions,name'],
        ];
    }
}
