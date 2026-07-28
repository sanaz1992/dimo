<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\ProductSku;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_sku_id',
        'order_id',
        'order_item_id',
        'inventory_reservation_id',
        'type',
        'quantity',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
