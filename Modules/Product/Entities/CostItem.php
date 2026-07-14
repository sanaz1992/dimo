<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class CostItem extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_active',
        'base_price',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getCreatedAtDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_cost_items')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
