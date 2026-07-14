<?php

namespace Modules\Product\Services\ProductPriceStrategy;

use Modules\Product\Entities\Product;
use Modules\User\Entities\User;

class SellerPriceService
{
    public function purchasePrice(Product $product, User $seller): int
    {
        $basePrice = (int) $product->final_price;

        $percent = (float) ($seller->sellerLevel?->percent ?? 0);

        return (int) round($basePrice * (1 + ($percent / 100)));
    }
}
