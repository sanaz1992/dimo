<?php

namespace Modules\User\Requests;

use Modules\Core\Http\Requests\BaseFormRequest;

class UpdateUserImageRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
        ];
    }
}
