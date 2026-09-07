<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;
use Modules\Instagram\Enums\AutomationActionRunStatus;

class AutomationActionRun extends Model
{
    use Filterable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'automation_run_id',
        'automation_action_id',
        'status',
        'error',
        'context',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => AutomationActionRunStatus::class,
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

    public function automationRun()
    {
        return $this->belongsTo(AutomationRun::class);
    }

    public function automationAction()
    {
        return $this->belongsTo(AutomationAction::class);
    }
}
