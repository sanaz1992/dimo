<?php

namespace Modules\Product\Http\Livewire\Admin\Product;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Livewire\Concerns\CreatesProduct;

class ProductEdit extends AdminBaseComponent
{
    use CreatesProduct;
    protected $queryString = ['currentStep', 'type'];


    public function mount(Product $product)
    {
        // $this->authorize( 'products_edit');
        $this->loadInitialData($product);
    }

    public function render()
    {
        return $this->renderView('Product::livewire.admin.product.product-edit')
            ->layoutData([
                'title' => __('product::attributes.edit_product'),
            ]);
    }
}
