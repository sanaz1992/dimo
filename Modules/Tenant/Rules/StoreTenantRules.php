<?php

namespace Modules\Tenant\Rules;

use Illuminate\Validation\Rules\Enum;
use Modules\Core\Enums\LocalEnum;
use Modules\Core\Enums\TimeZoneEnum;

class StoreTenantRules
{
    public static function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.timezone' => ['required', 'string', 'max:30', new Enum(TimeZoneEnum::class)],
            'form.local' => ['required', 'string', 'max:15', new Enum(LocalEnum::class)],
        ];
    }
}
