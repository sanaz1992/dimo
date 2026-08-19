<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Instagram\Enums\InstagramAccountStatus;
use Modules\Tenant\Entities\Tenant;

// use Modules\User\Database\Factories\AddressFactory;

class InstagramAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        // 'facebook_page_id',
        'instagram_account_id',
        'username',
        'name',
        'profile_picture_url',
        'access_token',
        'token_expires_at',
        'scops',
        'status',
        'connected_at',
        'last_synced_at',
    ];

    protected $casts = [
        'status' => InstagramAccountStatus::class,
        'access_token' => 'encrypted', // رمزنگاری خودکار در دیتابیس
        'scopes' => 'array',
        'token_expires_at' => 'datetime',
        'connected_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
