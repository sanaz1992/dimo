<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Modules\Core\Traits\Filterable;
use Modules\Marketing\Entities\Lead;


class CustomerLevel extends Authenticatable
{
    use Filterable;

    protected $fillable = [
        'title',
        'slug',
        'default',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'customer_level_id');
    }
}
