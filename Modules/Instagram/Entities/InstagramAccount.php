<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\User\Database\Factories\AddressFactory;

class InstagramAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id', 'instagram_account_id',
        'username', 'name', 'profile_picture_url',
        'access_token', 'token_expires_at', 'scops', 'status', 'connected_at', 'last_synced_at',
    ];

    // public function city(): BelongsTo
    // {
    //     return $this->belongsTo(City::class);
    // }
}
