<?php

namespace Modules\User\Http\Livewire\Admin\User;

use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Rules\StoreUserRules;
use Modules\User\Services\UserService;

class UserCreate extends AdminBaseComponent
{
    use WithFileUploads;
    // use Authorizable;
    use LivewireNotify;
    public $message;
    public $form = [
        'name'     => '',
        'mobile'   => '',
        'password' => '',
        'level'    => '',
        'image'    => null,
        'active' => ''

    ];

    public function mount()
    {
        // $this->authorize('users_create');
    }

    public function store(UserService $userService)
    {
        try {
            $this->validate(StoreUserRules::rules(), trans('user::validation'), trans('user::attributes'));
            $userService->create($this->form);
            $this->notify('success', __('core::messages.create.success'));
            $this->reset('form');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }
    public function render(UserService $userService)
    {
        $users = $userService->list(null, [10, true]);

        return $this->renderView('User::livewire.admin.user.user-create', compact('users'))
            ->layoutData([
                'title' => __('user::attributes.users_list')
            ]);
    }
}
