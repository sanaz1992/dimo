<?php

namespace Modules\ACL\Http\Livewire;

use Illuminate\Validation\ValidationException;
use Modules\ACL\Rules\UpdateUserRolesRules;
use Modules\ACL\Services\PermissionService;
use Modules\ACL\Services\RoleService;
use Modules\ACL\Services\UserRoleService;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Entities\User;
use Modules\User\Enums\UserLevel;

class UserRolesEdit extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use LivewireNotify;

    public User $user;

    public $message;

    public $roles;

    public $permissions;

    public $form = [
        'selectedRoles' => [],
        'selectedPermissions' => [],
    ];

    public function mount(User $user, RoleService $roleService, PermissionService $permissionService)
    {
        // $this->authorize('admins_roles_edit');

        if ($user->level != UserLevel::ADMIN->value) {
            $this->redirect(route('admin.users.index'));
        }
        $this->user = $user;
        $this->roles = $roleService->list();
        $this->permissions = $permissionService->list();

        $this->form['selectedRoles'] = $user->roles->pluck('name')->toArray();
        $this->form['selectedPermissions'] = $user->permissions->pluck('name')->toArray();
    }

    public function update(UserRoleService $userRoleService)
    {
        try {
            $this->validate(UpdateUserRolesRules::rules());
            $userRoleService->updateUserRoles($this->user, $this->form);
            $this->notify('success', __('core::messages.edit.success'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function render()
    {
        return $this->renderView('acl::livewire.user-roles-edit')
            ->layoutData([
                'title' => __('acl::attributes.user_roles_edit'),
            ]);
    }
}
