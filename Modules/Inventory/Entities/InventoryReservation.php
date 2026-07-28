<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Product\Entities\ProductSku;

class InventoryReservation extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_sku_id',
        'quantity',
        'status',
        'expires_at',
        'released_at',
        'converted_at',
        'expired_at',
        'meta',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
        'converted_at' => 'datetime',
        'expired_at' => 'datetime',
        'meta' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
