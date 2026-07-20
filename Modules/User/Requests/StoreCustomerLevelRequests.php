<?php

namespace Modules\User\Requests;

use Modules\Core\Http\Requests\BaseFormRequest;

class StoreCustomerLevelRequests extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'default' => ['required', 'bool'],
        ];
    }
}
