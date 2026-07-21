<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Category\Entities\Category;
use Modules\Core\Traits\Filterable;
use Modules\Media\Entities\Media;
use Modules\Media\Entities\NullMedia;
use Modules\Order\Entities\OrderItem;

class Product extends Model
{
    use Filterable;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'category_id',
        'description',
        'grade',
        'extraction_method',
        'is_active',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function uploadDir(): string
    {
        return 'uploads/products/'.$this->id;
    }

    public function getCreatedAtJalaliDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function mainImageRelation(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediaable')
            ->where('collection', 'main');
    }

    public function getMainImageAttribute()
    {
        if ($this->relationLoaded('mainImageRelation')) {
            return $this->getRelation('mainImageRelation') ?? new NullMedia;
        }

        return $this->mainImageRelation()->first() ?? new NullMedia;
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class);
    }

    public function getDefaultSku(): ?ProductSku
    {
        // اولویت ۱: ارزان‌ترین SKU که هم فعال است و هم موجودی دارد
        $sku = $this->skus()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('price', 'asc')
            ->first();

        if ($sku) {
            return $sku;
        }

        // اولویت ۲: ارزان‌ترین SKU فعال (حتی اگر موجودی آن صفر باشد)
        return $this->skus()
            ->where('is_active', true)
            ->orderBy('price', 'asc')
            ->first();
    }
}
