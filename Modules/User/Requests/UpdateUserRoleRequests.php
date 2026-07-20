<?php

namespace Modules\User\Requests;

use Modules\Core\Http\Requests\BaseFormRequest;

class UpdateUserRoleRequests extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roles' => ['nullable', 'array'],
            'roles.*' => ['nullable', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'exists:permissions,name'],
        ];
    }
}
