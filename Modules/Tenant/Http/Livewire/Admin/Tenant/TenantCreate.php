<?php

namespace Modules\Tenant\Http\Livewire\Admin\Tenant;

use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Tenant\Http\Livewire\Admin\Concerns\EditsTenant;
use Modules\Tenant\Services\TenantService;

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

            $this->validate(
                [
                    'form.user' => ['required', 'string', 'exists:users,unique_code'],
                ],
                trans('user::validation'),
                trans('user::attributes')
            );

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
            'Tenant::livewire.admin.tenant.tenant-create'
        )->layoutData([
            'title' => __('tenant::attributes.create_tenant'),
        ]);
    }
}
