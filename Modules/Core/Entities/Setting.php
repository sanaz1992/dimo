<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Media\Entities\Media;
use Modules\Media\Entities\NullMedia;

class Setting extends Model
{
    protected $fillable = [
        'title',
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    public function uploadDir(): string
    {
        return 'uploads/settings/'.$this->id;
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
}
