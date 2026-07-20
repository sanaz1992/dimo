<?php

namespace Modules\User\Requests;

use Modules\Core\Http\Requests\BaseFormRequest;

class CheckProvinceIdRequests extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'province_id' => ['required', 'exists:provinces,id'],
        ];
    }
}
