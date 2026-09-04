<?php

namespace Modules\Instagram\Http\Livewire\User\AutomationRules;

use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Http\Livewire\Concerns\ManagesAutomationRules;
use Modules\Tenant\Services\TenantService;

class UserAutomationRulesCreate extends UserBaseComponent
{
    use LivewireNotify;
    use ManagesAutomationRules;

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function getAvailableTenants()
    {
        return app(TenantService::class)->list(
            conditions: [
                'whereHas' => [
                    'users' => function ($query) {
                        $query->where(
                            'users.id',
                            auth()->id()
                        );
                    },
                ],
            ]
        );
    }

    public function render()
    {
        return $this->renderView(
            'Instagram::livewire.user.automation-rules.automation-rules-create'
        )->layoutData([
            'title' => __(
                'instagram::attributes.create_automation_rule'
            ),
        ]);
    }
}
