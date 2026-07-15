<?php

namespace Modules\Jetstream\Rules;

use Modules\Core\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRules extends FormRequest
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

    public static function rules(): array
    {
        return  [
            'name' => ['required', 'string', 'min:3'],
            'mobile' => ['required', 'string', 'max:11', new Mobile(), 'unique:users,mobile'],
            'captcha' => ['required', 'captcha'],
            'password' => ['required', 'min:8', 'string', 'confirmed']
        ];
    }
}
