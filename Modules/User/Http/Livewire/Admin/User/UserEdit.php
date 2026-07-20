<?php

namespace Modules\User\Http\Livewire\Admin\User;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Entities\User;
use Modules\User\Http\Livewire\Admin\Concerns\EditsUser;
use Modules\User\Services\UserService;

class UserEdit extends AdminBaseComponent
{
    use EditsUser;
    use LivewireNotify;

    public function mount(User $user)
    {
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
        return $this->renderView('User::livewire.admin.user.user-edit')
            ->layoutData([
                'title' => __('user::attributes.edit_user'),
            ]);
    }
}
