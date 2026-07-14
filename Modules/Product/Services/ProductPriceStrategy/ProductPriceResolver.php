<?php

namespace Modules\Product\Services\ProductPriceStrategy;

use Modules\Product\Entities\Product;

final class ProductPriceResolver
{
    /** @param ProductPriceStrategy[] $strategies */
    public function __construct(private array $strategies) {}

    public function resolve(Product $product): int
    {
        $hasStrategy = false;
        // اولویت: استراتژی‌های خاص‌تر اول
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($product)) {
                $hasStrategy = true;
                $productPrice = $strategy->getPrice($product);
            }
        }
        if (! $hasStrategy) { // نباید برسد اینجا اگر Fixed همیشه supports باشد
            $productPrice = (int) $product->price;
        }
        $costPrice = 0;
        foreach ($product->cost_items as $ci) {
            $costPrice += $ci->base_price * $ci->pivot->amount;
        }

        return $productPrice + $costPrice;
    }
}
