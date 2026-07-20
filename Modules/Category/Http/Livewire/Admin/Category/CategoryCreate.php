<?php

namespace Modules\Category\Http\Livewire\Admin\Category;

use Modules\Category\Http\Livewire\Concerns\CreatesCategory;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;

class CategoryCreate extends AdminBaseComponent
{
    use CreatesCategory;

    public function mount()
    {
        $this->loadInitialData();
    }

    public function render()
    {
        return $this->renderView(
            'Category::livewire.admin.category.category-create'
        )->layoutData([
            'title' => __('category::attributes.create_category')
        ]);
    }
}
