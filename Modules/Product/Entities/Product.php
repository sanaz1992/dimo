<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\Category\Entities\Category;
use Modules\Core\Traits\Filterable;
use Modules\Factory\Entities\ProductHallDifination;
use Modules\Media\Entities\Media;
use Modules\Media\Entities\NullMedia;
use Modules\Order\Entities\OrderItem;
use Modules\Product\Services\ProductPriceStrategy\ProductPriceResolver;
use Modules\Product\Services\ProductPriceStrategy\SellerPriceService;
use Modules\User\Enums\UserLevel;
use Modules\Warehouse\Entities\FinishedProductsWarehouse;
use Modules\Warehouse\Entities\PendingProductionWarehouse;
use Modules\Warehouse\Entities\RawMaterialWarehouse;

class Product extends Model
{
    use Filterable;

    protected $fillable = [
        'title',
        'slug',
        'code',
        'category_id',
        'description',
        'price',
        'is_publish',
        'has_fabric',
        'has_frame',
        'order_type',
        'is_intermediate',
    ];

    public array $historyFields = ['price'];

    // description in history table
    public ?string $historyDescription = null;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function uploadDir(): string
    {
        return 'uploads/products/'.$this->id;
    }

    public function getStatusAttribute()
    {
        if ($this->is_publish) {
            return [
                'class' => ' bg-green-50 text-green-600',
                'title' => __('product::attributes.published'),
            ];
        } else {
            return [
                'class' => ' bg-red-50 text-red-600',
                'title' => __('product::attributes.unpublished'),
            ];
        }
    }

    public function getFinalPriceAttribute(): int
    {
        return app(ProductPriceResolver::class)->resolve($this);
    }

    public function getEffectivePriceAttribute(): int
    {
        $user = auth()->user();

        if ($user?->level === UserLevel::SELLER->value) {
            return app(SellerPriceService::class)->purchasePrice($this, $user);
        }

        return (int) $this->final_price;
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

    public function materials(): MorphToMany
    {
        return $this->morphedByMany(
            RawMaterialWarehouse::class,
            'materialable',
            'product_materials'
        )->withPivot('qty');
    }

    public function intermediate_products(): MorphToMany
    {
        return $this->morphedByMany(
            Product::class,
            'materialable',
            'product_materials'
        )->withPivot('qty');
    }

    public function hall_difinations(): HasMany
    {
        return $this->hasMany(ProductHallDifination::class);
    }

    public function required_fabrics(): HasMany
    {
        return $this->hasMany(ProductRequiredFabric::class);
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cost_items(): BelongsToMany
    {
        return $this->belongsToMany(CostItem::class, 'product_cost_items')
            ->withPivot('amount');
    }

    public function finished_products_warehouses(): HasMany
    {
        return $this->hasMany(FinishedProductsWarehouse::class);
    }

    public function pendingProductionWarehouse(): MorphOne
    {
        return $this->morphOne(PendingProductionWarehouse::class, 'materialable');
    }
}
