<?php

namespace Modules\Product\Rules;

use Illuminate\Validation\Rule;

class UpdateProductRules extends StoreProductRules
{
    public static function rules(?int $id = null)
    {
        $rules = parent::rules();
        $rules['form.code'] = [
            'required',
            'max:255',
            Rule::unique('products', 'code')->ignore($id)
        ];
        return $rules;
    }
}
