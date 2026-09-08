<x-Instagram::automation-runs.automation-runs-table :title="__('instagram::attributes.automation_runs_list')"
    :automation-runs="$automationRuns"
    :user-can-show-detail="$authUser->can('automation_runs_show')" :selected-run="$selectedRun" :show-run-actions-modal="$showRunActionsModal" />
