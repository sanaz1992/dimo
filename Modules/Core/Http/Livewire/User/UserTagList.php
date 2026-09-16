<?php

namespace Modules\Core\Http\Livewire\User;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Filters\TagFilter;
use Modules\Core\Http\Livewire\Concerns\ManagesTags;
use Modules\Core\Services\TagService;

class UserTagList extends UserBaseComponent
{
    use ManagesTags;
    use WithPagination;

    protected $queryString = [
        'tenant',
    ];

    public $tenant = null;

    public $filterData = [];

    public $tenants;

    public function mount()
    {
        $authUser = auth()->user();
        $this->tenants = $authUser->tenants()->get();
    }

    #[On('updateTagListFilters')]
    public function handleFilters($filters)
    {
        $this->filterData = $filters;
        $this->resetPage();
    }

    public function fillFilterData()
    {
        $queryFilters = $this->queryString;
        foreach ($queryFilters as $filter) {
            if (! empty($this->{$filter})) {
                $this->filterData[$filter] ??= $this->{$filter};
            }
        }
    }

    public function render(TagService $tagService)
    {
        $this->fillFilterData();
        $this->filterData['user'] = auth()->user()->unique_code;
        $request = new Request($this->filterData ?? []);
        $filter = new TagFilter($request);

        $tags = $tagService->list(
            null,
            [10, true],
            filter: $filter
        );

        return $this->renderView('Core::livewire.user.tag.tag-list', compact('tags'))
            ->layoutData(['title' => __('core::attributes.tag_list')]);
    }
}
