<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Entities\ProductRequiredFabric;
use Modules\Warehouse\Entities\Color;
use Modules\Warehouse\Entities\FabricWarehouse;

class OrderItemFabric extends Model
{
    protected $fillable = [
        'order_item_id',
        'fabric_id',
        'color_id',
        'qty',
        'fabric_price',
        'product_required_fabric_id',
    ];

    public function order_item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function fabric(): BelongsTo
    {
        return $this->belongsTo(FabricWarehouse::class, 'fabric_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function product_required_fabric(): BelongsTo
    {
        return $this->belongsTo(ProductRequiredFabric::class);
    }
}
