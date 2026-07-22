<?php

namespace Modules\Cart\Entities;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
