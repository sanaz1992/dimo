<?php

namespace Modules\Tenant\Http\Livewire\User\Tenant;

use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Http\Livewire\Admin\Concerns\EditsTenant;
use Modules\Tenant\Services\TenantService;

class UserTenantEdit extends UserBaseComponent
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
            dd($e);
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function render()
    {
        return $this->renderView('Tenant::livewire.user.tenant.tenant-edit')
            ->layoutData([
                'title' => __('tenant::attributes.edit_tenant').' '.$this->tenant->name,
            ]);
    }
}
