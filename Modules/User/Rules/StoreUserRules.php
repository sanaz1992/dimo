<?php

namespace Modules\User\Rules;

use Illuminate\Validation\Rule;
use Modules\Core\Rules\Mobile;

class StoreUserRules
{
    public static function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.image' => ['nullable', 'image', 'max:2048'],
            'form.mobile' => ['required', 'string', 'max:11', new Mobile, Rule::unique('users', 'mobile')],
            'form.password' => ['required', 'confirmed', 'min:6', 'string'],
        ];
    }
}
