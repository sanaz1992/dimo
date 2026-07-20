<?php

namespace Modules\Category\Http\Livewire\Admin\Category;

use Livewire\WithPagination;
use Modules\Category\Services\CategoryService;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;

class CategoryList extends AdminBaseComponent
{
    use LivewireNotify;
    use WithPagination;

    public function mount() {}

    public function render(CategoryService $categoryService)
    {
        $categories = $categoryService->list(null, [], ['mainImageRelation']);

        return $this->renderView(
            'Category::livewire.admin.category.category-list',
            compact('categories')
        )->layoutData([
            'title' => __('category::attributes.category_list'),
        ]);
    }
}
