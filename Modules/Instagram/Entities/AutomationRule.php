<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;
use Modules\Instagram\Enums\AutomationMatchType;
use Modules\Instagram\Enums\AutomationTriggerType;
use Modules\Tenant\Entities\Tenant;

class AutomationRule extends Model
{
    use Filterable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'instagram_account_id',
        'instagram_post_id',
        'name',
        'trigger_type',
        'match_type',
        'match_value',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'trigger_type' => AutomationTriggerType::class,
        'match_type' => AutomationMatchType::class,
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function getCreatedAtJalaliAttribute()
    {
        return verta($this->created_at)->format('Y/m/d H:i');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function instagramAccount()
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    public function instagramPost()
    {
        return $this->belongsTo(InstagramPost::class);
    }

    public function actions()
    {
        return $this->hasMany(AutomationAction::class)->orderBy('sort_order');
    }

    public function runs()
    {
        return $this->hasMany(AutomationRun::class);
    }
}
