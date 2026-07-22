<?php

namespace Modules\Cart\Http\Livewire\Concerns;

use Modules\Cart\DTOs\AddToCartData;
use Modules\Cart\Services\CartManager;
use Modules\Core\Traits\LivewireNotify;

trait InteractsWithCart
{
    use LivewireNotify;

    public function addProductToCart(int $productId, ?int $skuId, int $quantity = 1): void
    {
        if (! $skuId) {
            $this->notify('error', 'لطفا یک گزینه را انتخاب کنید.');

            return;
        }

        app(CartManager::class)->add(
            new AddToCartData(
                productId: $productId,
                skuId: $skuId,
                quantity: $quantity,
            ),
            auth()->user()
        );

        $this->dispatch('cart-updated');

        if (method_exists($this, 'notify')) {
            $this->notify('success', 'محصول به سبد خرید اضافه شد.');
        }
    }
}
