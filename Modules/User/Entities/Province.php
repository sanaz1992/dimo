<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $fillable = ['name', 'slug', 'tel_prefix'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
