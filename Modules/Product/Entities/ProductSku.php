<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSku extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'packaging_type',
        'volume_ml',
        'price',
        'sale_price',
        'stock',
        'is_active',
    ];

    public array $historyFields = ['price', 'stock'];

    // description in history table
    public ?string $historyDescription = null;

    public function getRouteKeyName(): string
    {
        return 'sku';
    }

    public function getCreatedAtJalaliDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
