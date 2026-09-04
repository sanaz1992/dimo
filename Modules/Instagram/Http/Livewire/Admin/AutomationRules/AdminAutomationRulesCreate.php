<?php

namespace Modules\Instagram\Http\Livewire\Admin\AutomationRules;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Http\Livewire\Concerns\ManagesAutomationRules;
use Modules\Tenant\Services\TenantService;

class AdminAutomationRulesCreate extends AdminBaseComponent
{
    use LivewireNotify;
    use ManagesAutomationRules;

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function getAvailableTenants()
    {
        return app(TenantService::class)->list();
    }

    public function render()
    {
        return $this->renderView(
            'Instagram::livewire.admin.automation-rules.automation-rules-create'
        )->layoutData([
            'title' => __(
                'instagram::attributes.create_automation_rule'
            ),
        ]);
    }
}
