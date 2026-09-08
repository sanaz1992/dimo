<?php

namespace Modules\Instagram\Http\Livewire\Admin\AutomationRules;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Filters\AutomationRuleFilter;
use Modules\Instagram\Services\AutomationRuleService;

class AdminAutomationRulesList extends AdminBaseComponent
{
    use Authorizable;
    use LivewireNotify;
    use WithPagination;

    protected $queryString = [];

    public $filterData = [];

    public function mount()
    {
        $this->authorize('automation_rules_list');
    }

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
        $request = new Request($this->filterData ?? []);
        $filter = new AutomationRuleFilter($request);

        $automationRules = $automationRuleService->list(null, [10, true], with: ['instagramAccount', 'runs'], filter: $filter);

        return $this->renderView(
            'Instagram::livewire.admin.automation-rules.automation-rules-list',
            compact('automationRules')
        )->layoutData([
            'title' => __('instagram::attributes.automation_rules_list'),
        ]);
    }
}
