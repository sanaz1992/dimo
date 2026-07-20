<?php

namespace Modules\User\Rules;

use Illuminate\Validation\Rule;
use Modules\Core\Rules\Mobile;

class UpdateUserRules
{
    public static function rules(int $userId): array
    {
        return array_merge(StoreUserRules::rules(), [
            'form.mobile' => ['nullable', 'string', 'max:11', new Mobile, Rule::unique('users', 'mobile')->ignore($userId)],
            'form.password' => ['nullable', 'confirmed', 'min:6', 'string'],
        ]);
    }
}
