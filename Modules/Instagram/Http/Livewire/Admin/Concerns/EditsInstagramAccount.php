<?php

namespace Modules\Tenant\Http\Livewire\Admin\Concerns;

use Livewire\WithFileUploads;
use Modules\Core\Enums\LocalEnum;
use Modules\Core\Enums\TimeZoneEnum;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Rules\StoreTenantRules;
use Modules\Tenant\Services\TenantService;

trait EditsTenant
{
    use WithFileUploads;

    public Tenant $tenant;

    public $form = [
        'name' => '',
        'timezone' => '',
        'local' => '',
    ];

    public $timezones;

    public $locals;

    protected function fillForm(?Tenant $tenant = null): void
    {
        if ($tenant) {
            $this->tenant = $tenant;

            $this->form['name'] = $tenant->name;
            $this->form['timezone'] = $tenant->timezone;
            $this->form['local'] = $tenant->local;
        }

        $this->timezones = TimeZoneEnum::labels();
        $this->locals = LocalEnum::labels();
    }

    protected function tenantRules(): array
    {
        return StoreTenantRules::rules();
    }

    protected function validateTenant()
    {
        $this->validate(
            $this->tenantRules(),
            trans('user::validation'),
            trans('user::attributes')
        );
    }

    protected function createTenant(TenantService $tenantService)
    {
        return $tenantService->create($this->form);
    }

    public function updateTenant(TenantService $tenantService)
    {
        return $tenantService->update($this->tenant, $this->form);
    }
}
