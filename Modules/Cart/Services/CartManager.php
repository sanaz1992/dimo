<?php

namespace Modules\Cart\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Modules\Cart\DTOs\AddToCartData;
use Modules\Cart\Entities\Cart;
use Modules\Cart\External\Contracts\CartItemRepositoryInterface;
use Modules\Cart\External\Contracts\CartRepositoryInterface;
use Modules\Product\External\Contracts\ProductSkuRepositoryInterface;
use Modules\Product\Services\Pricing\ProductPriceResolver;
use RuntimeException;

class CartManager
{
    public function __construct(
        protected CartRepositoryInterface $cartRepository,
        protected CartItemRepositoryInterface $cartItemRepository,
        protected ProductSkuRepositoryInterface $skuRepository,
        protected ProductPriceResolver $priceResolver,
        protected CartSessionManager $sessionManager
    ) {}

    public function add(AddToCartData $data, ?Authenticatable $user = null): Cart
    {
        return DB::transaction(function () use ($data, $user) {
            $cart = $this->resolveCart($user);

            $sku = $this->skuRepository->findProductSku($data->productId, $data->skuId);

            if (! $sku) {
                throw new RuntimeException('Selected SKU is invalid.');
            }

            $item = $this->cartItemRepository->findByCartProductSku(
                $cart,
                $data->productId,
                $data->skuId
            );

            $newQuantity = $item ? $item->quantity + $data->quantity : $data->quantity;

            $price = $this->priceResolver->resolveForSku($sku, $newQuantity);

            if ($item) {
                $this->cartItemRepository->update($item, [
                    'quantity' => $newQuantity,
                    'unit_price' => $price->basePrice,
                    'discount_amount' => $price->discountAmount,
                    'final_price' => $price->finalPrice,
                ]);

                return $cart->fresh('items');
            }

            $this->cartItemRepository->create([
                'cart_id' => $cart->id,
                'product_id' => $data->productId,
                'product_sku_id' => $data->skuId,
                'quantity' => $data->quantity,
                'unit_price' => $price->basePrice,
                'discount_amount' => $price->discountAmount,
                'final_price' => $price->finalPrice,
            ]);

            return $cart->fresh('items');
        });
    }

    protected function resolveCart(?Authenticatable $user = null): Cart
    {
        if ($user) {
            return $this->cartRepository->getOrCreateActiveForUser($user);
        }

        return $this->cartRepository->getOrCreateActiveForToken(
            $this->sessionManager->getOrCreateToken()
        );
    }
}
