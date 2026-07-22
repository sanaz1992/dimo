<?php

namespace Modules\Shop\Http\Livewire;

use Modules\Cart\Http\Livewire\Concerns\InteractsWithCart;
use Modules\Category\Services\CategoryService;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;
use Modules\Product\Services\ProductService;

class HomePage extends GuestBaseComponent
{
    use InteractsWithCart;

    public $currency;

    public function mount()
    {
        $settingHelper = app(SettingHelper::class);
        $this->currency = $settingHelper->currencyLabel();
    }

    public function render()
    {
        $categories = resolve(CategoryService::class)->list(null, [6, false]);
        $products = resolve(ProductService::class)->list(
            null,
            [6, false],
            [
                'skus' => function ($query) {
                    $query->where('is_active', true);
                },
            ],
            [
                'whereHas' => ['skus' => function ($q) {
                    $q->where('is_active', true);
                }],
            ]
        );

        return $this->renderView(
            'Shop::livewire.guest.home',
            compact('categories', 'products')
        )->layoutData([
            'title' => __('shop::attributes.home_page'),
        ]);
    }
}
