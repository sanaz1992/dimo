<?php

namespace Modules\Cart\Entities;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_sku_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'final_price',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
