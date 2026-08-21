<?php

namespace Modules\Instagram\Http\Livewire\Admin\InstagramAccount;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Filters\InstagramAccountFilter;
use Modules\Instagram\Services\InstagramAccountService;

class InstagramAccountList extends AdminBaseComponent
{
    use LivewireNotify;
    use WithPagination;
    // use Authorizable;

    protected $queryString = [
        'tenant',
    ];

    public $tenant = null;

    public $filterData = [];

    public function mount()
    {
        $this->authorize('instagram_accounts_list');
    }

    #[On('updateInstagramAccountListFilters')]
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

    public function render(InstagramAccountService $instagramAccountService)
    {
        $this->fillFilterData();
        $request = new Request($this->filterData ?? []);
        $filter = new InstagramAccountFilter($request);

        $instagramAccounts = $instagramAccountService->list(null, [10, true], with: ['tenant'], filter: $filter);

        return $this->renderView('Instagram::livewire.admin.instagram-accounts.instagram-account-list', compact('instagramAccounts'))
            ->layoutData([
                'title' => __('instagram::attributes.instagram_accounts_list'),
            ]);
    }
}
