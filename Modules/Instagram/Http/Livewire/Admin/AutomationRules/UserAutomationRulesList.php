<?php

namespace Modules\Instagram\Http\Livewire\User\AutomationRules;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Filters\AutomationRuleFilter;
use Modules\Instagram\Services\AutomationRuleService;

class UserAutomationRulesList extends UserBaseComponent
{
    use LivewireNotify;
    use WithPagination;
    // use Authorizable;

    protected $queryString = [];

    public $filterData = [];

    public function mount() {}

    #[On('updateAutomationRuleListFilters')]
    public function handleFilters($filters)
    {
        $this->filterData = $filters;
        $this->resetPage();
    }

    public function fillFilterData()
    {
        // $queryFilters = [
        //     'tenant',
        //     // 'status',
        // ];
        $queryFilters = $this->queryString;

        foreach ($queryFilters as $filter) {
            if (! empty($this->{$filter})) {
                $this->filterData[$filter] ??= $this->{$filter};
            }
        }
    }

    public function render(AutomationRuleService $automationRuleService)
    {
        $this->fillFilterData();
        $this->filterData['user'] = auth()->user()->unique_code;
        $request = new Request($this->filterData ?? []);
        $filter = new AutomationRuleFilter($request);

        $automationRules = $automationRuleService->list(null, [10, true], with: ['instagramAccount'], filter: $filter);

        return $this->renderView(
            'Instagram::livewire.user.automation-rules.automation-rules-list',
            compact('automationRules')
        )
            ->layoutData([
                'title' => __('instagram::attributes.automation_rules_list'),
            ]);
    }
}
