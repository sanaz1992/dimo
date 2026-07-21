<?php

namespace Modules\Product\Http\Livewire\Guest\Product;

use Modules\Category\Services\CategoryService;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;
use Modules\Product\Entities\Product;
use Modules\Product\Services\Pricing\ProductPriceResolver;

class ProductDetail extends GuestBaseComponent
{
    public Product $product;

    public $currency;

    public int $quantity = 1;

    public ?int $selectedSkuId = null;

    public function mount(Product $product)
    {
        $settingHelper = app(SettingHelper::class);
        $this->currency = $settingHelper->currencyLabel();

        $product->load(['skus' => function ($query) {
            $query->where('is_active', true);
        }]);
        $this->product = $product;

        // set default sku
        $defaultSku = $this->product->skus->where('stock', '>', 0)->sortBy('price')->first()
            ?? $this->product->skus->sortBy('price')->first();

        if ($defaultSku) {
            $this->selectedSkuId = $defaultSku->id;
        }
    }

    public function selectSku(int $skuId)
    {
        $this->selectedSkuId = $skuId;
        // بازنشانی تعداد به ۱ در صورت تغییر حجم
        $this->quantity = 1;
    }

    public function incrementQty()
    {
        $selectedSku = $this->product->skus->firstWhere('id', $this->selectedSkuId);
        if ($selectedSku && $this->quantity < $selectedSku->stock) {
            $this->quantity++;
        }
    }

    public function decrementQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function getPriceDetailsProperty()
    {
        if (! $this->selectedSkuId) {
            return null;
        }

        $selectedSku = $this->product->skus->firstWhere('id', $this->selectedSkuId);

        if (! $selectedSku) {
            return null;
        }

        return app(ProductPriceResolver::class)->resolveForSku($selectedSku, $this->quantity);
    }

    public function render()
    {
        $category = resolve(CategoryService::class)->find($this->product->category_id);

        return $this->renderView(
            'Product::livewire.guest.product.product-detail',
            compact('category')
        )->layoutData([
            'title' => $this->product->name,
        ]);
    }
}
