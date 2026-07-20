<?php

namespace Modules\User\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\Core\Http\Requests\BaseFormRequest;
use Modules\Core\Rules\Mobile;
use Modules\User\Enums\UserLevel;

class StoreUserRequests extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => [
                'required',
                'string',
                'max:11',
                new Mobile,
                Rule::unique('users', 'mobile')->whereNull('deleted_at'),
            ],
            'password' => ['required', 'min:8', 'string'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'is_active' => ['nullable', 'bool'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'level' => ['nullable', new Enum(UserLevel::class)],
            'process' => ['nullable', 'exists:processes,slug'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:4096'],
        ];
    }
}
