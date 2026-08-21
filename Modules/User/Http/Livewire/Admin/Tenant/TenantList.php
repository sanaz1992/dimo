<?php

namespace Modules\User\Http\Livewire\Admin\Tenant;

use Illuminate\Validation\Rules\Enum;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Tenant\Enums\TenantStatus;
use Modules\User\Services\TenantService;

class TenantList extends AdminBaseComponent
{
    use LivewireNotify;
    use WithPagination;
    // use Authorizable;

    public $tenantStatuses;

    public $selectedTenant;

    public $form = [];

    public $showChangeStatusModal = false;

    public function mount()
    {
        $this->authorize('tenants_list');

        $this->tenantStatuses = TenantStatus::labels();
    }

    public function selectStatus($id)
    {
        $this->selectedTenant = app(TenantService::class)->findByColumn('id', $id);
        $this->form['status'] = $this->selectedTenant->status->value;
        $this->showChangeStatusModal = true;
    }

    public function updateItemStatus()
    {
        $this->validate(
            [
                'form.status' => ['required', 'string', 'max:30', new Enum(TenantStatus::class)],
            ],
            trans('user::validation'),
            trans('user::attributes')
        );
        try {
            app(TenantService::class)->update($this->selectedTenant, $this->form);
            $this->showChangeStatusModal = false;
            $this->notify('success', __('core::messages.edit.success'));
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function render(TenantService $tenantService)
    {
        $tenants = $tenantService->list(null, [10, true]);

        return $this->renderView('User::livewire.admin.tenant.tenant-list', compact('tenants'))
            ->layoutData([
                'title' => __('user::attributes.tenants_list'),
            ]);
    }
}
