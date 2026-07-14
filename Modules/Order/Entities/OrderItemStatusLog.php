<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Entities\User;

class OrderItemStatusLog extends Model
{
    protected $fillable = [
        'order_item_id',
        'status',
        'changed_by',
    ];

    public function order_item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function changed_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
