<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Entities\History;
use Modules\Core\Traits\Filterable;
use Modules\Inventory\Entities\InventoryReservation;
use Modules\Order\Enums\OrderStatus;
use Modules\User\Entities\Address;
use Modules\User\Entities\User;

class Order extends Model
{
    use Filterable;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'status',
        'payment_status',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => OrderStatus::class,
    ];

    public array $historyFields = ['status'];

    // description in history table
    public ?string $historyDescription = null;

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function getCreatedAtJalaliAttribute()
    {
        return verta($this->created_at)->format('Y/m/d H:i:s');
    }

    public function getUpdatedAtJalaliAttribute()
    {
        return verta($this->updated_at)->format('Y/m/d H:i:s');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function histories(): MorphMany
    {
        return $this->morphMany(History::class, 'historiable');
    }

    public function inventoryReservations()
    {
        return $this->hasMany(InventoryReservation::class);
    }
}
