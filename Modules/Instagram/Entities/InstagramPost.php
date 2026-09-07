<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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
        'comments_count',
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

    public function getCaptionSummeryAttribute()
    {
        return Str::limit($this->caption, 50);
    }

    public function instagramAccount()
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    public function comments()
    {
        return $this->hasMany(InstagramComment::class);
    }

    public function automationRules()
    {
        return $this->hasMany(AutomationRule::class);
    }
}
