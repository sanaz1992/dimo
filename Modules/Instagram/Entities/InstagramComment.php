<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;

class InstagramComment extends Model
{
    use Filterable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'instagram_post_id',
        'instagram_account_id',
        'instagram_comment_id',
        'commenter_ig_id',
        'commenter_username',
        'comment_text',
        'commented_at',
        'payload',
    ];

    protected $casts = [
        'commented_at' => 'datetime',
        'payload' => 'array',
    ];

    public function getCommentedAtJalaliAttribute()
    {
        return verta($this->commented_at)->format('Y/m/d H:i');
    }

    public function instagramPost()
    {
        return $this->belongsTo(InstagramPost::class);
    }

    public function instagramAccount()
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    public function automationRuns()
    {
        return $this->hasMany(AutomationRun::class);
    }
}
