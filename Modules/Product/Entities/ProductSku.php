<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Enums\ProductPackagingType;

class ProductSku extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'packaging_type',
        'volume_ml',
        'price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'packaging_type' => ProductPackagingType::class,
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

    // public function getPackagingTypeTitleAttribute()
    // {
    //     return ProductPackagingType::labels($this->packaging_type);
    // }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
