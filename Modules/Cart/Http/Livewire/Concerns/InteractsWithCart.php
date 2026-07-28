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
        try {
            app(CartManager::class)->add(
                new AddToCartData(
                    productId: $productId,
                    skuId: $skuId,
                    quantity: $quantity,
                ),
                auth()->user()
            );

            $this->dispatch('cart-updated');
            $this->notify('success', 'محصول به سبد خرید اضافه شد.');

        } catch (\RuntimeException $e) {
            $this->notify('error', $e->getMessage());

            return;
        } catch (\Exception $e) {
            $this->notify('error', $e->getMessage());

            return;
        }

    }
}
