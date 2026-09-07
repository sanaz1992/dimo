<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Enums\SyncRunStatus;
use Modules\Core\Enums\SyncRunType;
use Modules\Core\Traits\Filterable;
use Modules\Tenant\Entities\Tenant;

class SyncRun extends Model
{
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'syncable_type',
        'syncable_id',
        'type',
        'status',
        'total',
        'processed',
        'started_at',
        'completed_at',
        'failed_at',
        'error',
        'meta',
    ];

    protected $casts = [
        'status' => SyncRunStatus::class,
        'type' => SyncRunType::class,
        'total' => 'integer',
        'processed' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function syncable()
    {
        return $this->morphTo();
    }

    public function getCreatedAtJalaliAttribute()
    {
        return verta($this->created_at)->format('Y/m/d H:i');
    }

    public function getStartedAtJalaliAttribute()
    {
        return $this->started_at
            ? verta($this->started_at)->format('Y/m/d H:i')
            : null;
    }

    public function getCompletedAtJalaliAttribute()
    {
        return $this->completed_at
            ? verta($this->completed_at)->format('Y/m/d H:i')
            : null;
    }

    public function getFailedAtJalaliAttribute()
    {
        return $this->failed_at
            ? verta($this->failed_at)->format('Y/m/d H:i')
            : null;
    }

    public function isPending(): bool
    {
        return $this->status === SyncRunStatus::PENDING;
    }

    public function isRunning(): bool
    {
        return $this->status === SyncRunStatus::RUNNING;
    }

    public function isCompleted(): bool
    {
        return $this->status === SyncRunStatus::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === SyncRunStatus::FAILED;
    }

    public function isFinished(): bool
    {
        return in_array(
            $this->status,
            [
                SyncRunStatus::COMPLETED,
                SyncRunStatus::FAILED,
                SyncRunStatus::CANCELLED,
            ],
            true
        );
    }

    public function getProgressPercentageAttribute(): ?int
    {
        if (! $this->total || $this->total <= 0) {
            return null;
        }

        return min(100, (int) round(($this->processed / $this->total) * 100));
    }
}
