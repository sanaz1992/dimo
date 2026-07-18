<?php

namespace Modules\Product\Http\Livewire\Admin\Product;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Product\Http\Livewire\Concerns\CreatesProduct;

class ProductCreate extends AdminBaseComponent
{
    use CreatesProduct;

    public function mount()
    {
        $this->loadInitialData();
    }


    public function render()
    {
        return $this->renderView(
            'Product::livewire.admin.product.product-create'
        )->layoutData([
            'title' => __('product::attributes.create_product')
        ]);
    }
}
