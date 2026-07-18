<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Category\Enums\CategoryType;
use Modules\Media\Entities\Media;
use Modules\Media\Entities\NullMedia;
use Modules\Product\Entities\Product;
use Modules\Warehouse\Entities\RawMaterialWarehouse;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'type',];

    protected $casts = [
        'type' => CategoryType::class
    ];

    public function uploadDir(): string
    {
        return 'uploads/categories';
    }

    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function mainImageRelation(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediaable')
            ->where('collection', 'main');
    }
    public function getMainImageAttribute()
    {
        if ($this->relationLoaded('mainImageRelation')) {
            return $this->getRelation('mainImageRelation') ?? new NullMedia();
        }

        return $this->mainImageRelation()->first() ?? new NullMedia();
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterialWarehouse::class);
    }
}
