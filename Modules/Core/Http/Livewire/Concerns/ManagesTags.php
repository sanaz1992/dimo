<?php

namespace Modules\Core\Http\Livewire\Concerns;

trait ManagesTags
{
    public array $form = [
        'tenant' => '',
        'instagram_account' => '',
        'instagram_post_id' => '',
        'name' => '',
        'trigger_type' => '',
        'match_type' => '',
        'match_value' => '',
        'is_active' => '',
        'priority' => '',
    ];
}
