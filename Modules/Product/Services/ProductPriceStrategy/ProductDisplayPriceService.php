<?php

namespace Modules\Product\Services\ProductPriceStrategy;

use Modules\Product\Entities\Product;
use Modules\User\Entities\User;
use Modules\User\Enums\UserLevel;

class ProductDisplayPriceService
{
    public function __construct(
        protected SellerPriceService $sellerPriceService
    ) {}

    public function resolve(Product $product, ?User $user = null): int
    {
        if ($user?->level == UserLevel::SELLER->value) {
            return $this->sellerPriceService->purchasePrice($product, $user);
        }

        return (int) $product->final_price;
    }
}
