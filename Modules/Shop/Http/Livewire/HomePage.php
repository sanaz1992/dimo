<?php

namespace Modules\Shop\Http\Livewire;

use Modules\Category\Services\CategoryService;
use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;
use Modules\Product\Services\ProductService;

class HomePage extends GuestBaseComponent
{
    public function mount() {}

    public function render()
    {
        $categories = resolve(CategoryService::class)->list(null, [6, false]);
        $products = resolve(ProductService::class)->list(null, [6, false]);

        return $this->renderView(
            'Shop::livewire.guest.home',
            compact('categories', 'products')
        )->layoutData([
            'title' => __('shop::attributes.home_page'),
        ]);
    }
}
