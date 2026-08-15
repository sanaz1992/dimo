<?php

namespace Modules\Transactions\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\Filterable;
use Modules\Order\Entities\Order;
use Modules\Transactions\Enums\TransactionGateway;
use Modules\Transactions\Enums\TransactionStatus;

class Transaction extends Model
{
    use Filterable;

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'reference_id',
        'amount',
        'status',
        'payload',
        'authority',
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
        'payload' => 'array',
        'gateway' => TransactionGateway::class,
    ];

    public function getCreatedAtJalaliAttribute()
    {
        return verta($this->created_at)->format('Y/m/d H:i:s');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
