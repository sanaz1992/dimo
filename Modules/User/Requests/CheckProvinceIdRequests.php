<?php

namespace Modules\User\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Rules\Mobile;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Core\Http\Requests\BaseFormRequest;

class CheckProvinceIdRequests extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return  [
            'province_id' => ['required', 'exists:provinces,id'],
        ];
    }
}
