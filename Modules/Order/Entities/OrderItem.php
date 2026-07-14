<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Factory\Entities\ProductionOrderItem;
use Modules\Media\Entities\Media;
use Modules\Product\Entities\Product;
use Modules\Shipment\Entities\ShipmentItem;
use Modules\Warehouse\Entities\Color;
use Modules\Warehouse\Entities\FabricWarehouse;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'discount',
        'price',
        'qty',
        'status',
        'custom_frame',
        'send_fabric_by_customer',
        'frame_color_id',
        'total_price'
    ];

    public function getUpdatedAtJalaliAttribute()
    {
        return verta($this->updated_at)->format('Y/m/d H:i:s');
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function item_fabrics(): HasMany
    {
        return $this->hasMany(OrderItemFabric::class);
    }

    public function frame_color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'frame_color_id');
    }

    public function production_order_items(): HasMany
    {
        return $this->hasMany(ProductionOrderItem::class);
    }

    public function status_logs(): HasMany
    {
        return $this->hasMany(OrderItemStatusLog::class, 'order_item_id');
    }

    public function shipment_items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }
}
