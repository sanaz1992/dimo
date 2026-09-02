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
        'unique_code',
        'tenant_id',
        'instagram_account_id',
        'customer_ig_id',
        'customer_username',
        'status',
        'last_message_at',
        'customer_profile_picture_url',
    ];

    protected $casts = [
        'status' => ConversationStatus::class,
        'last_message_at' => 'datetime',
    ];

    public function getLastMessageAtJalaliAttribute()
    {
        return verta($this->last_message_at)->format('Y/m/d H:i');
    }

    public function getAvatarColorAttribute(): string
    {
        $colors = [
            'A' => '#b47841',
            'B' => '#4d71b9',
            'C' => '#684ead',
            'D' => '#3caca4',
            'E' => '#b1751c',
            'F' => '#b93434',
            'G' => '#144671',
            'H' => '#82af30',
            'I' => '#b63089',
            'J' => '#2d6cb0',
            'K' => '#a45505',
            'L' => '#2f64ae',
            'M' => '#73309d',
            'N' => '#279c92',
            'O' => '#6a0619',
            'P' => '#3a7907',
            'Q' => '#60076a',
            'R' => '#e2d33f',
            'S' => '#d46600',
            'T' => '#036846',
            'U' => '#6e0037',
            'V' => '#003855',
            'W' => '#6e2c00',
            'X' => '#011d66',
            'Y' => '#782800',
            'Z' => '#144800',
        ];
        $firstLetter = strtoupper(mb_substr($this->customer_username, 0, 1));

        return $colors[$firstLetter] ?? '#64748B';
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function instagramAccount()
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
