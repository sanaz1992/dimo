<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Entities\ProductSku;

class PurchaseItem extends Model
{
    protected $fillable = [
        'product_sku_id',
        'purchase_id',
        'quantity',
        'purchase_price',
        'sale_price',
        'total_cost',
    ];

    public function getCreatedAtJalaliDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }

    public function product_sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
