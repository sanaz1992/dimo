<?php

namespace Modules\Instagram\Http\Livewire\User\InstagramPost;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Filters\InstagramPostFilter;
use Modules\Instagram\Jobs\SyncInstagramPosts;
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
        if (! $this->account) {
            return;
        }

        $instagramAccount = app(InstagramAccountService::class)->findByColumn('unique_code', $this->account);

        if (! $instagramAccount) {
            $this->notifyError('اکانت اینستاگرام پیدا نشد.');

            return;
        }

        SyncInstagramPosts::dispatch($instagramAccount->id);

        $this->notify('success', 'بروزرسانی پست‌ها در پس‌زمینه شروع شد.');
    }
}
