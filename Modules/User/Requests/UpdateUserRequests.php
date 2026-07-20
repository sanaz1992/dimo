<?php

namespace Modules\User\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Rules\Mobile;

class UpdateUserRequests extends StoreUserRequests
{
    public function rules(): array
    {
        $uniqueCode = $this->route('user') ?? $this->route('admin');

        return array_merge(parent::rules(), [
            'mobile' => [
                'required',
                'string',
                'max:11',
                new Mobile,
                Rule::unique('users', 'mobile')->ignore($uniqueCode, 'unique_code'),
            ],
            'password' => ['nullable', 'min:8', 'string'],
            'process' => ['nullable', 'exists:processes,slug'],
        ]);
    }
}
