<?php

namespace Modules\Cart\External;

use Modules\Cart\Entities\Cart;
use Modules\Cart\Entities\CartItem;
use Modules\Cart\External\Contracts\CartItemRepositoryInterface;
use Modules\Core\External\Repositories\BaseRepository;

class CartItemRepository extends BaseRepository implements CartItemRepositoryInterface
{
    public function __construct(CartItem $model)
    {
        parent::__construct($model);
    }

    public function findByCartProductSku(Cart $cart, int $productId, int $skuId): ?CartItem
    {
        return $cart->items()
            ->where('product_id', $productId)
            ->where('product_sku_id', $skuId)
            ->first();
    }
}
