<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;
use Modules\Instagram\Enums\InstagramMediaType;

class InstagramPost extends Model
{
    use Filterable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'instagram_account_id',
        'instagram_media_id',
        'media_product_type',
        'caption',
        'permalink',
        'published_at',
        'payload',
    ];

    protected $casts = [
        'media_product_type' => InstagramMediaType::class,
        'published_at' => 'datetime',
        'payload' => 'array',
    ];

    public function getPublishedAtJalaliAttribute()
    {
        return verta($this->published_at)->format('Y/m/d H:i');
    }

    public function instagramAccount()
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    public function comments()
    {
        return $this->hasMany(InstagramComment::class);
    }
}
