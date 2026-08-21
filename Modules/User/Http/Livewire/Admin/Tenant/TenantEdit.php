<?php

namespace Modules\User\Http\Livewire\Admin\Tenant;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Tenant\Entities\Tenant;
use Modules\User\Http\Livewire\Admin\Concerns\EditsTenant;
use Modules\User\Services\TenantService;

class TenantEdit extends AdminBaseComponent
{
    use EditsTenant;
    use LivewireNotify;

    public function mount(Tenant $tenant)
    {
        $this->fillForm($tenant);
    }

    public function store(TenantService $tenantService)
    {
        try {
            $this->validateTenant();
            $this->updateTenant($tenantService);
            $this->notify('success', __('core::messages.edit.success'));
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function render()
    {
        return $this->renderView('User::livewire.admin.tenant.tenant-edit')
            ->layoutData([
                'title' => __('user::attributes.edit_tenant').' '.$this->tenant->name,
            ]);
    }
}
