<?php

namespace Modules\ACL\Http\Livewire;

use Illuminate\Validation\ValidationException;
use Modules\ACL\Entities\Permission;
use Modules\ACL\Rules\StoreRoleRules;
use Modules\ACL\Services\RoleService;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;

class RoleCreate extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use LivewireNotify;
    public $permissions;
    public $form = [
        'title'               => '',
        'name'                => '',
        'selectedPermissions' => [],
    ];

    public function mount()
    {
        // $this->authorize('roles_create');
        $this->permissions = Permission::get();
    }

    public function store(RoleService $roleService)
    {
        try {
            $this->validate(
                StoreRoleRules::rules(),
                trans('acl::validation'),
                trans('acl::attributes')
            );

            $roleService->create($this->form);

            $this->notify('success', __('core::messages.create.success'));
            // $this->reset('form');
            return redirect()->route('admin.roles.create');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }

    public function render()
    {
        return $this->renderView('acl::livewire.role-create')
            ->layoutData([
                'title' => __('acl::attributes.create')
            ]);
    }
}
