<?php

namespace Modules\Core\Http\Livewire\Admin;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Filters\TagFilter;
use Modules\Core\Http\Livewire\Concerns\ManagesTags;
use Modules\Core\Services\TagService;
use Modules\Tenant\Entities\Tenant;

class AdminTagList extends AdminBaseComponent
{
    use Authorizable;
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
        $this->authorize('tags_list');

        $this->tenants = Tenant::query()->orderBy('name')->get();
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
        $request = new Request($this->filterData ?? []);
        $filter = new TagFilter($request);

        $tags = $tagService->list(
            null,
            [10, true],
            filter: $filter
        );

        return $this->renderView('Core::livewire.admin.tag.tag-list', compact('tags'))
            ->layoutData(['title' => __('core::attributes.tag_list')]);
    }
}
