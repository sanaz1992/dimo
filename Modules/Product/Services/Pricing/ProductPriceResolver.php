<?php

// Modules/Product/Services/Pricing/ProductPriceResolver.php

namespace Modules\Product\Services\Pricing;

use Modules\Product\Contracts\DiscountCalculator;
use Modules\Product\DataTransferObjects\ProductPrice;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSku;

final readonly class ProductPriceResolver
{
    public function __construct(
        private DiscountCalculator $discountCalculator
    ) {}

    /**
     * ۱. محاسبه قیمت دقیق یک SKU مشخص (برای صفحه جزئیات، سبد خرید، نهایی‌سازی سفارش)
     */
    public function resolveForSku(ProductSku $sku, int $quantity = 1): ProductPrice
    {
        $basePrice = $sku->price;
        $discountAmount = $this->discountCalculator->calculateDiscount($sku, $quantity);

        // اطمینان از اینکه تخفیف از قیمت اصلی بیشتر نشود
        $discountAmount = min($discountAmount, $basePrice);
        $finalPrice = max(0, $basePrice - $discountAmount);

        $discountPercentage = $basePrice > 0 ? (int) round(($discountAmount / $basePrice) * 100) : 0;
        $hasStock = $sku->stock >= $quantity;

        return new ProductPrice(
            basePrice: $basePrice,
            discountAmount: $discountAmount,
            finalPrice: $finalPrice,
            discountPercentage: $discountPercentage,
            hasStock: $hasStock,
            isFromPrice: false
        );
    }

    /**
     * ۲. محاسبه قیمت پیش‌فرض محصول (برای نمایش روی کارت محصول در لیست‌ها)
     */
    public function resolveForList(Product $product): ?ProductPrice
    {
        // لود کردن رابطه از رم در صورت وجود (جلوگیری از N+1)
        $skus = $product->relationLoaded('skus') ? $product->skus : $product->skus()->get();

        if ($skus->isEmpty()) {
            return null;
        }

        // پیدا کردن SKU پیش‌فرض بر اساس اولویت‌ها روی Collection لود شده (بدون کوئری مجدد)
        // اولویت اول: ارزان‌ترین SKU فعال و موجود
        $defaultSku = $skus->where('is_active', true)->where('stock', '>', 0)->sortBy('price')->first()
            // اولویت دوم: ارزان‌ترین SKU فعال (حتی ناموجود)
            ?? $skus->where('is_active', true)->sortBy('price')->first();

        if (! $defaultSku) {
            return null;
        }

        // مشخص کردن اینکه آیا محصول تنوع قیمتی دارد؟ (برای نمایش برچسب "شروع از")
        $hasMultiplePrices = $skus->where('is_active', true)->pluck('price')->unique()->count() > 1;

        // محاسبه قیمت SKU پیش‌فرض
        $price = $this->resolveForSku($defaultSku, 1);

        // برگرداندن DTO نهایی همراه با مشخص کردن وضعیت "شروع از"
        return new ProductPrice(
            basePrice: $price->basePrice,
            discountAmount: $price->discountAmount,
            finalPrice: $price->finalPrice,
            discountPercentage: $price->discountPercentage,
            hasStock: $price->hasStock,
            isFromPrice: $hasMultiplePrices
        );
    }
}
