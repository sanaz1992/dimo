<?php

namespace Modules\Product\DataTransferObjects;

final readonly class ProductPrice
{
    public function __construct(
        public int $basePrice,          // قیمت اصلی SKU
        public int $discountAmount,     // مقدار تخفیف
        public int $finalPrice,         // قیمت نهایی (پرداختی)
        public int $discountPercentage, // درصد تخفیف
        public bool $hasStock,          // موجودی دارد؟
        public bool $isFromPrice, // آیا قیمت «شروع از...» است؟ (برای کارت محصول)
        public int $skuId
    ) {}
}
