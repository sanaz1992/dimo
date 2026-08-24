<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\Filterable;
use Modules\Instagram\Enums\WebhookEventStatus;
use Modules\Tenant\Entities\Tenant;

// use Modules\User\Database\Factories\AddressFactory;

class WebhookEvent extends Model
{
    use Filterable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'instagram_account_id',
        'provider',
        'event_type',
        'event_key',
        'payload',
        'status',
        'error',
        'processed_at',
        'attempts',
    ];

    protected $casts = [
        'status' => WebhookEventStatus::class,
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function getProcessedAtAtJalaliAttribute()
    {
        return verta($this->processed_at)->format('Y/m/d');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function instagramAccount(): BelongsTo
    {
        return $this->belongsTo(
            InstagramAccount::class,
            'instagram_account_id'
        );
    }
}
