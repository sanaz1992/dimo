<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;
use Modules\Instagram\Enums\ConversationStatus;
use Modules\Tenant\Entities\Tenant;

class Conversation extends Model
{
    use Filterable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'instagram_account_id',
        'customer_ig_id',
        'customer_username',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'status' => ConversationStatus::class,
        'last_message_at' => 'datetime',
    ];

    public function getLastMessageAtJalaliAttribute()
    {
        return verta($this->last_message_at)->format('Y/m/d');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
