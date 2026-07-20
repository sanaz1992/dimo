<?php

namespace Modules\Dashboard\Http\Livewire\Admin;

use Livewire\Attributes\On;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Enums\UserLevel;

class AdminDashboard extends AdminBaseComponent
{
    use LivewireNotify;

    public $tab;

    public function mount()
    {
        $user = auth()->user();
        if ($user->level != UserLevel::ADMIN->value) {
            $this->notify('error', __('core::messages.access_error'));

            return redirect()->route('logout');
        }
    }

    #[On('tabChanged')]
    public function setTab(string $tab)
    {
        $this->setColumns();
    }

    public function setColumns() {}

    public function render()
    {
        return $this->renderView(
            'dashboard::livewire.admin.admin-dashboard'
        )->layoutData([
            'title' => __('core::attributes.dashboard'),
        ]);
    }
}
