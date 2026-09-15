<?php

namespace Modules\Core\Http\Livewire\User;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Filters\TagFilter;
use Modules\Core\Services\TagService;
use Modules\Core\Traits\LivewireNotify;
use Modules\Tenant\Entities\Tenant;

class UserTagList extends UserBaseComponent
{
    use LivewireNotify;
    use WithPagination;
    // use Authorizable;

    protected $queryString = [
        'tenant',
    ];

    public $tenant = null;

    public $filterData = [];

    public $tenants;

    public $form = [
        'edit_tag' => false,
        'tenant' => '',
        'name' => '',
        'color' => '',
        'is_active' => true,
    ];

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

    public function render(TagService $tagService)
    {
        $this->fillFilterData();
        $this->filterData['user'] = auth()->user()->unique_code;

        $request = new Request($this->filterData ?? []);
        $filter = new TagFilter($request);

        $tags = $tagService->list(null, [10, true], filter: $filter);

        return $this->renderView(
            'Core::livewire.user.tag.tag-list',
            compact('tags')
        )->layoutData([
            'title' => __('core::attributes.tag_list'),
        ]);
    }

    public $showTagCreateModal = false;

    public function createTag()
    {
        $this->form['edit_tag'] = false;
        $this->showTagCreateModal = true;
    }

    public function storeTag(TagService $tagService)
    {
        try {
            $this->validate([
                'form.tenant' => ['required', 'exists:tenants,slug'],
                'form.name' => ['required', 'string'],
                'form.color' => ['nullable', 'string', 'max:7'],
                'form.is_active' => ['required', 'boolean'],
            ]);
            if ($this->form['edit_tag']) {
                $tagService->update($this->selectedTag, $this->form);
                $this->notify('success', __('core::messages.edit.success'));
            } else {
                $tagService->create($this->form);
                $this->notify('success', __('core::messages.create.success'));
            }

            $this->showTagCreateModal = false;
            $this->reset('form');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            $this->notify('error', __('core::messages.error'));
        }
    }

    public $selectedTag = null;

    public function editTag($slug)
    {
        $this->selectedTag = app(TagService::class)->findByColumn('slug', $slug);
        $this->form['edit_tag'] = true;
        $this->form['tenant'] = $this->selectedTag->tenant->slug;
        $this->form['name'] = $this->selectedTag->name;
        $this->form['color'] = $this->selectedTag->color;
        $this->form['is_active'] = $this->selectedTag->is_active;
        $this->showTagCreateModal = true;
    }
}
