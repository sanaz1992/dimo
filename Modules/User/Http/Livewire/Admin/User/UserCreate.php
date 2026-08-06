<?php

namespace Modules\User\Http\Livewire\Admin\User;

use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Http\Livewire\Admin\Concerns\CreatesUser;
use Modules\User\Services\UserService;

class UserCreate extends AdminBaseComponent
{
    use CreatesUser;
    use LivewireNotify;

    public function mount()
    {
        $this->loadFormData();
    }

    public function store(UserService $userService)
    {
        try {
            $this->validateUser();

            $this->createUser(
                $userService
            );

            $this->notify('success', __('core::messages.create.success'));
            $this->reset('form');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->notify('error', __('core::messages.create.error'));
        }
    }

    public function render()
    {
        return $this->renderView(
            'User::livewire.admin.user.user-create'
        )->layoutData([
            'title' => __('user::attributes.new_user'),
        ]);
    }
}
