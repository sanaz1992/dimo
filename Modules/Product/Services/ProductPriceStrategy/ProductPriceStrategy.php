<?php

namespace Modules\Product\Services\ProductPriceStrategy;

use Modules\Product\Entities\Product;

interface ProductPriceStrategy
{
    public function supports(Product $product): bool;

    public function getPrice(Product $product): int;
}
