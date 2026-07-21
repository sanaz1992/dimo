<?php

namespace Modules\Product\Services\Pricing;

use Modules\Product\Contracts\DiscountCalculator;
use Modules\Product\Entities\ProductSku;

final class NullDiscountCalculator implements DiscountCalculator
{
    /**
     * این متد همیشه مقدار ۰ را به عنوان تخفیف برمی‌گرداند.
     */
    public function calculateDiscount(ProductSku $sku, int $quantity = 1): int
    {
        return 0;
    }
}
