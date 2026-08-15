<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\Filterable;
use Modules\User\Entities\User;

class Purchase extends Model
{
    use Filterable;

    protected $fillable = [
        'supplier_id',
        'purchased_at',
        'status',
        'invoice_number',
        'created_by',
    ];

    public function getRouteKeyName(): string
    {
        return 'invoice_number';
    }

    public function getCreatedAtJalaliAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }

    public function getPurchasedAtJalaliAttribute()
    {
        return verta($this->purchased_at)->format('Y/m/d');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
