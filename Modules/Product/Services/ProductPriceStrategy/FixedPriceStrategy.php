<?php

namespace Modules\Product\Services\ProductPriceStrategy;

use Modules\Product\Entities\Product;

final class FixedPriceStrategy implements ProductPriceStrategy
{
    public function supports(Product $product): bool
    {
        return ! (bool) cache('settings')->where('key', 'auto_calculate_product_cost')->first()->value;
    }

    public function getPrice(Product $product): int
    {
        return (int) $product->price;
    }
}
