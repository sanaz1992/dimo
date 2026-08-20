<?php

namespace Modules\Tenant\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Tenant\Enums\TenantStatus;
use Modules\User\Entities\User;

// use Modules\User\Database\Factories\AddressFactory;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'timezone',
        'local',
        'status',
    ];

    protected $casts = [
        'status' => TenantStatus::class,
    ];

    public function instagramAccounts()
    {
        return $this->hasMany(InstagramAccount::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
