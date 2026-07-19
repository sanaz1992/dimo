<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;

class Purchase extends Model
{
    use Filterable;

    protected $fillable = [
        'supplier_id',
        'purchased_at',
        'status',
    ];

    public function getCreatedAtJalaliDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }
}
