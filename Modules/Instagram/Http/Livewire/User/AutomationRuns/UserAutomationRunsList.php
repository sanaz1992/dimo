<?php

namespace Modules\Instagram\Http\Livewire\User\AutomationRuns;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Filters\AutomationRunFilter;
use Modules\Instagram\Services\AutomationRunService;

class UserAutomationRunsList extends UserBaseComponent
{
    use LivewireNotify;
    use WithPagination;
    // use Authorizable;

    protected $queryString = [
        'account',
    ];

    public $account = null;

    public $filterData = [];

    public function mount() {}

    #[On('updateAutomationRunListFilters')]
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

    public function render(AutomationRunService $automationRunService)
    {
        $this->fillFilterData();
        $authUser = auth()->user();
        $authUser->load('tenants');
        $tenantsId = $authUser->tenants->pluck('id')->toArray();
        $this->filterData['tenants'] = $tenantsId;
        $request = new Request($this->filterData ?? []);
        $filter = new AutomationRunFilter($request);

        $automationRuns = $automationRunService->list(null, [10, true], with: [
            'automationRule',
            'instagramAccount',
            'instagramComment',
        ], filter: $filter);

        return $this->renderView(
            'Instagram::livewire.user.automation-runs.automation-runs-list',
            compact('automationRuns')
        )->layoutData([
            'title' => __('instagram::attributes.automation_runs_list'),
        ]);
    }

    public $selectedRun = null;

    public $showRunActionsModal = false;

    public function showRunDetail($id)
    {
        $this->selectedRun = app(AutomationRunService::class)->findByColumn('id', $id);
        if (! $this->selectedRun) {
            $this->notify('error', 'اجرای مورد نظر یافت نشد.');
        }
        $this->selectedRun->load([
            'automationRule',
            'instagramAccount',
            'instagramComment',
            'actionRuns.automationAction',
        ]);
        $this->showRunActionsModal = true;
    }
}
