<?php

namespace Modules\Instagram\Http\Livewire\User\InstagramPost;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Entities\SyncRun;
use Modules\Core\Enums\SyncRunType;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Services\SyncRunService;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Instagram\Filters\InstagramPostFilter;
use Modules\Instagram\Services\InstagramAccountService;
use Modules\Instagram\Services\InstagramPostService;

class UserInstagramPostList extends UserBaseComponent
{
    use LivewireNotify;
    use WithPagination;

    protected $queryString = [
        'account',
    ];

    public $account = null;

    public $filterData = [];

    public ?SyncRun $postsSyncRun = null;

    public $postsSyncRuns = [];

    public function mount() {}

    #[On('updateInstagramPostListFilters')]
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

    public function render(InstagramPostService $instagramPostService)
    {
        $this->fillFilterData();
        $this->filterData['user'] = auth()->user()->unique_code;
        $request = new Request($this->filterData ?? []);
        $filter = new InstagramPostFilter($request);

        $instagramPosts = $instagramPostService->list(null, [10, true], with: ['instagramAccount'], filter: $filter);

        return $this->renderView(
            'Instagram::livewire.user.instagram-posts.instagram-post-list',
            compact('instagramPosts')
        )->layoutData([
            'title' => __('instagram::attributes.my_instagram_posts_list'),
        ]);
    }

    public function syncInstagramPosts(): void
    {
        $postService = app(InstagramPostService::class);

        // Sync selected account
        if ($this->account) {
            $instagramAccount = app(InstagramAccountService::class)->findByColumn('unique_code', $this->account);
            if (! $instagramAccount) {
                $this->notify('error', __('instagram::messages.the_selected_instagram_account_could_not_be_found'));

                return;
            }

            $syncRun = $postService->startInstagramPostsSync($instagramAccount);
            if (! $syncRun) {
                $this->notify('error', __('instagram::messages.this_accounts_posts_are_currently_being_updated'));

                return;
            }

            $this->postsSyncRun = $syncRun;

            $this->notify('success', __('instagram::messages.post_update_has_started_in_the_background'));

            return;
        }

        // Sync all accounts
        $authUser = auth()->user();
        $authUser->load('tenants');
        $tenans = $authUser->tenants;

        $instagramAccounts = app(InstagramAccountService::class)->list(conditions: [
            'whereIn' => ['tenant_id' => [$tenans->pluck('id')->toArray()]],
        ]);
        if ($instagramAccounts->isEmpty()) {
            $this->notify('error', __('instagram::messages.no_instagram_accounts_are_connected'));

            return;
        }

        $started = 0;
        foreach ($instagramAccounts as $instagramAccount) {
            $syncRun = $postService->startInstagramPostsSync($instagramAccount);
            if ($syncRun) {
                $started++;
            }
        }

        if ($started === 0) {
            $this->notify('error', __('instagram::messages.post_update_is_currently_in_progress'));

            return;
        }

        $this->postsSyncRuns = app(SyncRunService::class)
            ->getActiveRunsForTenants($tenans->pluck('id')->toArray(), 'instagram_posts');

        $this->notify('success', __('instagram::messages.post_update_has_started_in_the_background'));
    }

    public function refreshPostsSyncStatus(): void
    {
        $syncRunService = app(SyncRunService::class);

        // Sync selected account
        if ($this->account) {
            $instagramAccount = app(InstagramAccountService::class)->findByColumn('unique_code', $this->account);
            if (! $instagramAccount) {
                return;
            }
            $this->postsSyncRun = $syncRunService->getLatestRun(
                InstagramAccount::class,
                $instagramAccount->id,
                SyncRunType::INSTAGRAM_POSTS->value
            );

            return;
        }

        // Sync all accounts
        $authUser = auth()->user();
        $authUser->load('tenants');
        $tenantIds = $authUser->tenants->pluck('id')->toArray();
        $this->postsSyncRuns = $syncRunService->getActiveRunsForTenants($tenantIds, SyncRunType::INSTAGRAM_POSTS->value);
    }
}
