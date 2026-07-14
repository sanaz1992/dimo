<?php

namespace Modules\Product\Services\ProductPriceStrategy;

use Modules\Product\Entities\Product;

final class SmartCostStrategy implements ProductPriceStrategy
{
    private $settings;

    public function __construct()
    {
        $this->settings = cache('settings');
    }

    public function supports(Product $product): bool
    {
        return (bool) $this->settings->where('key', 'auto_calculate_product_cost')->first()->value;
    }

    public function getPrice(Product $product): int
    {
        $product->loadMissing(['materials', 'intermediate_products']);

        $sum = 0;
        foreach ($product->materials as $ri) {
            $sum += (int) ($ri->price * $ri->pivot->qty);
        }

        foreach ($product->intermediate_products as $ip) {
            $sum += (int) ($ip->final_price * $ip->pivot->qty);
        }

        return $sum;
    }
}
