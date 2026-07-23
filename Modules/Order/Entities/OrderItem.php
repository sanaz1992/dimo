<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Entities\ProductSku;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_sku_id',
        'quantity',
        'price',
        'discount',
        'total',
    ];

    public function getUpdatedAtJalaliAttribute()
    {
        return verta($this->updated_at)->format('Y/m/d H:i:s');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product_sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class);
    }
}
