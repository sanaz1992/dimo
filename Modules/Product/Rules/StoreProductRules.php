<?php

namespace Modules\Product\Rules;

class StoreProductRules
{
    public static function rules(?int $id = null)
    {
        return [
            'form.title' => ['required', 'string', 'max:255'],
            'form.code' => ['required', 'string', 'max:255', 'unique:products,code'],
            'form.image' => ['nullable', 'image', 'max:'.config('media.validations.image.max'), 'mimes:'.config('media.validations.image.mimes')],
            'form.category_id' => ['required', 'exists:categories,id'],
            'form.description' => ['nullable', 'string', 'max:255'],
            'form.price' => ['required', 'numeric'],
            'form.has_fabric' => ['required', 'boolean'],
            'form.has_frame' => ['required', 'boolean'],
            // 'form.is_active'   => ['required', 'in:1,0'],
            'form.is_intermediate' => ['required', 'boolean'],
        ];
    }
}
