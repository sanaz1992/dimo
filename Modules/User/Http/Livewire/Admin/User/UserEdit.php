<?php

namespace Modules\User\Http\Livewire\Admin\User;

use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Entities\User;
use Modules\User\Rules\UpdateUserRules;
use Modules\User\Services\UserService;

class UserEdit extends AdminBaseComponent
{
    use WithFileUploads;
    // use Authorizable;
    use LivewireNotify;
    public User $user;
    public $form = [
        'name'     => '',
        'mobile'   => '',
        'password' => '',
        'level'    => '',
        'image'    => null,
        'active' => ''

    ];

    // public $permissions;
    public $message;
    public function mount(User $user)
    {
        // $this->authorize('users_edit');

        $this->user           = $user;
        $this->form['name']   = $user->name;
        $this->form['mobile'] = $user->mobile;
        $this->form['level']  = $user->level;
        $this->form['active'] = (bool) $user->active;
    }

    public function update(UserService $userService)
    {
        try {
            $this->validate(UpdateUserRules::rules($this->user->id), trans('user::validation'), trans('user::attributes'));
            $userService->update($this->user, $this->form);
            $this->notify('success', __('core::messages.edit.success'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }
    public function render(UserService $userService)
    {
        return $this->renderView('User::livewire.admin.user.user-edit')
            ->layoutData([
                'title' => __('user::attributes.users_edit')
            ]);
    }
}
