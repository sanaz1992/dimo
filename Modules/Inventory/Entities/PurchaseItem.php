<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'product_sku_id',
        'purchase_id',
        'quantity',
        'purchase_price',
        'total_cost',
    ];

    public function getCreatedAtJalaliDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }
}
