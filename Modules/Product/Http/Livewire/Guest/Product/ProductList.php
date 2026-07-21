<?php

namespace Modules\Product\Http\Livewire\Guest\Product;

use Livewire\WithPagination;
use Modules\Category\Entities\Category;
use Modules\Category\Services\CategoryService;
use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;
use Modules\Product\Services\ProductService;

class ProductList extends GuestBaseComponent
{
    use WithPagination;

    public ?Category $category = null;

    public $search = '';

    public $sort = 'latest';

    public function mount(?Category $category = null)
    {
        if ($category && $category->exists) {
            $this->category = $category;
        }
    }

    /**
     * گرفتن لیست محصولات با اعمال فیلترها
     */
    public function getProductsProperty()
    {
        $conditions = [];
        if ($this->category) {
            $conditions = array_merge($conditions, [
                'where' => ['category_id' => ['=', $this->category->id]],
            ]);
        }

        return resolve(ProductService::class)->list(
            null,
            [12, true],
            [],
            $conditions
        );
    }

    public function render()
    {
        $categories = resolve(CategoryService::class)->list(conditions: ['where' => ['is_active' => ['=', true]]]);

        return $this->renderView('Product::livewire.guest.product.product-list', [
            'products' => $this->products,
            'categories' => $categories,
        ])->layoutData([
            'title' => __('product::attributes.products'),
        ]);
    }
}
