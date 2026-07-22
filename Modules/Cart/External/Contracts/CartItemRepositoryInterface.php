<?php

namespace Modules\Cart\External\Contracts;

use Modules\Cart\Entities\Cart;
use Modules\Cart\Entities\CartItem;
use Modules\Core\External\Repositories\Contract\BaseRepositoryInterface;

interface CartItemRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCartProductSku(Cart $cart, int $productId, int $skuId): ?CartItem;
}
