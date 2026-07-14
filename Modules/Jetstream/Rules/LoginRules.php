<?php

namespace Modules\Jetstream\Rules;

use Modules\Core\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;

class LoginRules extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return  [
            'mobile'    => ['required', 'string', 'max:11', new Mobile()],
            'password' => ['required', 'string', 'min:8'],
            'captcha' => ['required', 'captcha']
        ];
    }
}
