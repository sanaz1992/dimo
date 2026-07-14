<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Mobile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^09\d{9}$/', $value)) {
            $fail(__('core::validation.ir_mobile'));
        }
    }
}
