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

    public function mount()
    {
        $this->authorize('instagram_accounts_list');
    }

    public $filterData;

    #[On('updateInstagramAccountListFilters')]
    public function handleFilters($filters)
    {
        dd($filters);
        $this->filterData = $filters;
    }

    public function render(InstagramAccountService $instagramAccountService)
    {
        $request = new Request($this->filterData ?? []);
        $filter = new InstagramAccountFilter($request);

        $instagramAccounts = $instagramAccountService->list(null, [10, true], with: ['tenant'], filter: $filter);

        return $this->renderView('Instagram::livewire.admin.instagram-accounts.instagram-account-list', compact('instagramAccounts'))
            ->layoutData([
                'title' => __('instagram::attributes.instagram_accounts_list'),
            ]);
    }
}
