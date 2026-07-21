<?php

namespace Modules\Product\Contracts;

use Modules\Product\Entities\ProductSku;

interface DiscountCalculator
{
    /**
     * محاسبه مبلغ تخفیف برای یک SKU خاص
     */
    public function calculateDiscount(ProductSku $sku, int $quantity = 1): int;
}
