<?php

namespace Modules\Category\Rules;

use Illuminate\Validation\Rules\Enum;
use Modules\Category\Enums\CategoryType;

class StoreCategoryRules
{
    public static function rules()
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.type' => ['required', 'string', new Enum(CategoryType::class)],
            'form.image' => ['nullable', 'image', 'max:2048'],
            'form.parent_id' => ['nullable', 'exists:categories,id']
        ];
    }
}
