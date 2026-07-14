<?php

namespace Modules\ACL\Http\Livewire;

use Livewire\WithPagination;
use Modules\ACL\Services\RoleService;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;

class RoleList extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use   WithPagination;
    public function mount()
    {
        // $this->authorize('roles_list');
    }
    public function render(RoleService $roleService)
    {
        $roles = $roleService->list(null, [10, true], ['permissions']);

        return $this->renderView('acl::livewire.role-list', compact('roles'))
            ->layoutData([
                'title' => __('acl::attributes.list')
            ]);
    }
}
