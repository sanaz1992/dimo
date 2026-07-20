<?php

namespace Modules\Jetstream\Rules;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Rules\Mobile;

class LoginCheckCodeRules extends FormRequest
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
        return [
            'mobile' => ['required', 'string', 'max:11', new Mobile],
            'code' => ['required', 'numeric', 'min:100000', 'max:10000000'],
        ];
    }
}
