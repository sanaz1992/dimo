<?php

namespace Modules\User\Http\Livewire\Admin\Tenant;

use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Http\Livewire\Admin\Concerns\EditsTenant;
use Modules\User\Services\TenantService;

class TenantCreate extends AdminBaseComponent
{
    use EditsTenant;
    use LivewireNotify;

    public function mount()
    {
        $this->fillForm();
    }

    public function store(TenantService $tenantService)
    {

        try {
            $this->validateTenant();

            $this->createTenant(
                $tenantService
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
            'User::livewire.admin.tenant.tenant-create'
        )->layoutData([
            'title' => __('user::attributes.create_tenant'),
        ]);
    }
}
