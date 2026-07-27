<?php

namespace Modules\Cart\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Entities\Address;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'status',
        'address_id',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
