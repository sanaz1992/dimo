<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Core\Entities\History;
use Modules\Core\Entities\Note;
use Modules\Core\Traits\Filterable;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\OrderType;
use Modules\Shipment\Entities\ShipmentItem;
use Modules\User\Entities\User;

class Order extends Model
{
    use Filterable;

    protected $fillable = [
        'code',
        'seller_id',
        'user_id',
        'subtotal',
        'discount',
        'total_price',
        'description',
        'status',
        'loading_code',
        'type',
    ];

    public array $historyFields = ['status'];

    // description in history table
    public ?string $historyDescription = null;

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function getCreatedAtJalaliDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }

    public function getUpdatedAtJalaliDateAttribute()
    {
        return verta($this->updated_at)->format('Y/m/d');
    }

    public function isManual(): bool
    {
        return $this->type === OrderType::MANUAL->value;
    }

    /**
     * A manual/imported order is "pending pricing" while any of its line
     * items still has no price entered (price is null).
     */
    public function getIsPendingPricingAttribute(): bool
    {
        if (! $this->isManual()) {
            return false;
        }

        if ($this->relationLoaded('items')) {
            return $this->items->contains(fn ($item) => is_null($item->price));
        }

        return $this->items()->whereNull('price')->exists();
    }

    public function getCodeLinkAttribute()
    {
        static $authUser;
        $authUser ??= auth()->user();

        if ($this->status == OrderStatus::DRAFT->value) {
            if ($authUser->can('orders_edit')) {
                $href = route('admin.orders.edit', $this);
            }
        } else {
            if ($authUser->can('orders_show')) {
                $href = route('admin.orders.show', $this);
            }
        }

        return $href ?? '#';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function shipment_items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function histories(): MorphMany
    {
        return $this->morphMany(History::class, 'historiable');
    }
}
