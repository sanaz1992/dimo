<?php

namespace Modules\Instagram\Http\Livewire\Admin\InstagramPost;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Filters\InstagramPostFilter;
use Modules\Instagram\Services\InstagramPostService;

class AdminInstagramPostList extends AdminBaseComponent
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
        $this->authorize('instagram_posts_list');
    }

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
        $request = new Request($this->filterData ?? []);
        $filter = new InstagramPostFilter($request);

        $instagramPosts = $instagramPostService->list(null, [10, true], with: ['instagramAccount'], filter: $filter);

        return $this->renderView(
            'Instagram::livewire.admin.instagram-posts.instagram-post-list',
            compact('instagramPosts')
        )->layoutData([
            'title' => __('instagram::attributes.my_instagram_posts_list'),
        ]);
    }
}
