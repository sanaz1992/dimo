<?php

namespace Modules\Dashboard\Http\Livewire\User;

use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;

class UserDashboard extends UserBaseComponent
{
    use LivewireNotify;

    public function mount() {}

    public function render()
    {
        return $this->renderView('dashboard::livewire.user.user-dashboard')
            ->layoutData([
                'title' => __('core::attributes.user_dashboard'),
            ]);
    }
}
