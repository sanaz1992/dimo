<?php

namespace Modules\ACL\Http\Livewire;

use Illuminate\Validation\ValidationException;
use Modules\ACL\Entities\Permission;
use Modules\ACL\Entities\Role;
use Modules\ACL\Rules\UpdateRoleRules;
use Modules\ACL\Services\RoleService;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;

class RoleEdit extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use LivewireNotify;
    public Role $role;
    public $form = [
        'title'               => '',
        'selectedPermissions' => [],
    ];

    public $permissions;
    public $message;
    public function mount(Role $role)
    {
        // $this->authorize('roles_edit');
        $this->role                        = $role;
        $this->form['title']               = $role->title;
        $this->form['selectedPermissions'] = $role->permissions->pluck('name')->toArray();
        $this->permissions                 = Permission::get();
    }

    public function update(RoleService $roleService)
    {
        try {
            $this->validate(UpdateRoleRules::rules());

            $roleService->update($this->role, $this->form);
            $this->notify('success', __('core::messages.edit.success'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }
    public function render()
    {
        return $this->renderView('acl::livewire.role-edit')
            ->layoutData([
                'title' => __('acl::attributes.roles_edit')
            ]);
    }
}
