<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Media\Entities\Media;
use Modules\User\Entities\User;

class History extends Model
{
    protected $fillable = [
        'user_id',
        'historiable_type',
        'historiable_id',
        'field',
        'old_value',
        'new_value',
        'action',
        'description'
    ];

    public function historiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
