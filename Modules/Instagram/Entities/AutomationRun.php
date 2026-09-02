<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;
use Modules\Instagram\Enums\AutomationRunStatus;

class AutomationRun extends Model
{
    use Filterable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'automation_rule_id',
        'instagram_account_id',
        'instagram_comment_id',
        'status',
        'error',
        'context',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => AutomationRunStatus::class,
        'context' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getCreatedAtJalaliAttribute()
    {
        return verta($this->created_at)->format('Y/m/d H:i');
    }

    public function getStartedAtJalaliAttribute()
    {
        return verta($this->started_at)->format('Y/m/d H:i');
    }

    public function getCompletedAtJalaliAttribute()
    {
        return verta($this->completed_at)->format('Y/m/d H:i');
    }

    public function automationRule()
    {
        return $this->belongsTo(AutomationRule::class);
    }

    public function instagramAccount()
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    public function instagramComment()
    {
        return $this->belongsTo(InstagramComment::class);
    }
}
