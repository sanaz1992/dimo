<?php

namespace Modules\Category\Http\Livewire\Admin\Category;

use Modules\Category\Entities\Category;
use Modules\Category\Http\Livewire\Concerns\CreatesCategory;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;

class CategoryEdit extends AdminBaseComponent
{
    use CreatesCategory;

    public function mount(Category $category)
    {
        $this->loadInitialData($category);
    }

    public function render()
    {
        return $this->renderView(
            'Category::livewire.admin.category.category-edit'
        )->layoutData([
            'title' => __('category::attributes.edit_category').' '.$this->category->name,
        ]);
    }
}
