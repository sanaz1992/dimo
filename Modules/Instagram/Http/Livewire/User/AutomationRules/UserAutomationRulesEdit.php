<?php

namespace Modules\Instagram\Http\Livewire\User\AutomationRules;

use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Entities\AutomationRule;
use Modules\Instagram\Http\Livewire\Concerns\ManagesAutomationRules;
use Modules\Tenant\Services\TenantService;

class UserAutomationRulesEdit extends UserBaseComponent
{
    use LivewireNotify;
    use ManagesAutomationRules;

    public function mount(AutomationRule $automationRule): void
    {
        $this->fillForm($automationRule);

        $this->currentStep = request()->query('step', 'basic');
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

    protected function getAutomationRuleEditRoute(AutomationRule $automationRule): string
    {
        return route('user.automation_rules.edit', ['automationRule' => $automationRule]);
    }

    public function render()
    {
        return $this->renderView('Instagram::livewire.user.automation-rules.automation-rules-edit')
            ->layoutData([
                'title' => __('instagram::attributes.edit_automation_rule')
                    .' '
                    .($this->automationRule ? ': '.$this->automationRule->name : ''),
            ]);
    }
}
