<?php

namespace Modules\Instagram\Rules;

use Illuminate\Validation\Rules\Enum;
use Modules\Instagram\Enums\AutomationMatchType;
use Modules\Instagram\Enums\AutomationTriggerType;

class StoreAutomationRuleRules
{
    public static function rules(): array
    {
        return [
            'form.tenant' => ['required', 'string', 'exists:tenants,slug'],
            'form.instagram_account' => ['required', 'string', 'exists:instagram_accounts,unique_code'],
            'form.instagram_post' => ['nullable', 'string', 'exists:instagram_posts'],
            'form.name' => ['required', 'string', 'max:255'],
            'form.trigger_type' => ['required', 'string', 'max:30', new Enum(AutomationTriggerType::class)],
            'form.match_type' => ['required', 'string', 'max:50', new Enum(AutomationMatchType::class)],
            'form.match_value' => ['required', 'string', 'max:255'],
            'form.is_active' => ['required', 'string', 'in:0,1'],
            'form.priority' => ['required', 'numeric'],
        ];
    }
}
