<?php

namespace Modules\Instagram\Http\Livewire\Admin\AutomationRuns;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Enums\AutomationRunStatus;
use Modules\Instagram\Filters\AutomationRunFilter;
use Modules\Instagram\Services\AutomationRunService;
use Modules\Instagram\Services\AutomationService;

class AdminAutomationRunsList extends AdminBaseComponent
{
    use Authorizable;
    use LivewireNotify;
    use WithPagination;

    protected $queryString = [
        'account',
    ];

    public $account = null;

    public $filterData = [];

    public function mount()
    {
        $this->authorize('automation_runs_list');
    }

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
        $request = new Request($this->filterData ?? []);
        $filter = new AutomationRunFilter($request);

        $automationRuns = $automationRunService->list(null, [10, true], with: [
            'automationRule',
            'instagramAccount',
            'instagramComment',
        ], filter: $filter);

        return $this->renderView(
            'Instagram::livewire.admin.automation-runs.automation-runs-list',
            compact('automationRuns')
        )->layoutData([
            'title' => __('instagram::attributes.automation_runs_list'),
        ]);
    }

    public $selectedRun = null;

    public $showRunActionsModal = false;

    public function showRunDetail($id)
    {
        if (! auth()->user()->can('automation_runs_show')) {
            $this->notify('error', __('core::messages.you_do_not_have_permission_to_perform_this_action'));

            return;
        }

        $this->selectedRun = app(AutomationRunService::class)->findByColumn('id', $id);
        if (! $this->selectedRun) {
            $this->notify('error', __('instagram::messages.the_requested_execution_was_not_found'));
        }
        $this->selectedRun->load([
            'automationRule',
            'instagramAccount',
            'instagramComment',
            'actionRuns.automationAction',
        ]);
        $this->showRunActionsModal = true;
    }

    public function retryRun(int $id): void
    {
        if (! auth()->user()->can('automation_runs_retry')) {
            $this->notify('error', __('instagram::messages.you_do_not_have_permission'));

            return;
        }

        $run = app(AutomationRunService::class)->findByColumn('id', $id);
        if (! $run) {
            $this->notify('error', __('instagram::messages.the_requested_execution_was_not_found'));

            return;
        }

        if ($run->status !== AutomationRunStatus::FAILED) {
            $this->notify('error', __('instagram::messages.only_failed_executions_can_be_retried'));

            return;
        }

        // Retry logic goes here
        try {
            app(AutomationService::class)->retryRun($run);
            $this->notify('success', __('instagram::messages.execution_retry_started'));
        } catch (\Throwable $e) {
            Log::error('Failed to retry automation run.', [
                'run_id' => $run->id,
                'error' => $e->getMessage(),
            ]);

            $this->notify('error', __('instagram::messages.execution_retry_failed'));
        }

        $this->notify('success', __('instagram::messages.execution_retry_started'));

        $this->resetPage();
    }
}
