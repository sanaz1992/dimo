<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Media\Entities\Media;
use Modules\Media\Entities\NullMedia;
use Modules\Product\Entities\Product;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'description'];

    public function uploadDir(): string
    {
        return 'uploads/categories';
    }

    public function getCreatedAtJalaliDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
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
}
