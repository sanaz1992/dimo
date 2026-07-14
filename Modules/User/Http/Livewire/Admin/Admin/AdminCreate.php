<?php

namespace Modules\User\Http\Livewire\Admin\Admin;

use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Enums\UserLevel;
use Modules\User\Http\Livewire\Admin\Concerns\CreatesUser;
use Modules\User\Services\UserService;

class AdminCreate extends AdminBaseComponent
{
    use CreatesUser;
    // use Authorizable;
    use LivewireNotify;


    public function mount()
    {
        // $this->authorize('admins_create');
    }


    public function store(UserService $userService)
    {
        try {
            $this->validateUser();

            $this->createUser(
                $userService,
                UserLevel::ADMIN->value
            );

            $this->notify('success', __('core::messages.create.success'));

            return redirect()->route('admin.admins.create');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }
    public function render(UserService $userService)
    {
        $users = $userService->list(null, [10, true]);

        return $this->renderView(
            'User::livewire.admin.admin.admin-create',
            compact('users')
        )->layoutData([
            'title' => __('user::attributes.create_admin')
        ]);
    }
}
