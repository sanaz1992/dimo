<?php

namespace Modules\Cart\DTOs;

class AddToCartData
{
    public function __construct(
        public readonly int $productId,
        public readonly int $skuId,
        public readonly int $quantity = 1,
    ) {}
}
