<?php

namespace Modules\Transactions\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;

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
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function getCreatedAtJalaliDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }
}
