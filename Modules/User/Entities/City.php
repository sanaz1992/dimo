<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $fillable = ['name', 'slug', 'province_id'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
