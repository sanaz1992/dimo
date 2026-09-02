<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;
use Modules\Instagram\Enums\AutomationActionType;

class AutomationAction extends Model
{
    use Filterable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'automation_rule_id',
        'action_type',
        'sort_order',
        'config',
        'is_active',
    ];

    protected $casts = [
        'action_type' => AutomationActionType::class,
        'config' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getCreatedAtJalaliAttribute()
    {
        return verta($this->created_at)->format('Y/m/d H:i');
    }

    public function automationRule()
    {
        return $this->belongsTo(AutomationRule::class);
    }
}
