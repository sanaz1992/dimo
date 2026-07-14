<?php

namespace Modules\User\Http\Livewire\Admin\Admin;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Entities\User;
use Modules\User\Http\Livewire\Admin\Concerns\EditsUser;
use Modules\User\Services\UserService;

class AdminEdit extends AdminBaseComponent
{
    // use Authorizable;
    use LivewireNotify;
    use EditsUser;

    public function mount(User $user)
    {
        // $this->authorize('admins_edit');
        $this->fillUserForm($user);
    }

    public function store(UserService $userService)
    {
        try {
            $this->validateUser();
            $this->updateUser($userService);
            $this->notify('success', __('core::messages.edit.success'));
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function render()
    {
        return $this->renderView('User::livewire.admin.admin.admin-edit')
            ->layoutData([
                'title' => __('user::attributes.admins_edit')
            ]);
    }
}
