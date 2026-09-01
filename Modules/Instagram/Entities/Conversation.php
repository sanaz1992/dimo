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
        return verta($this->last_message_at)->format('Y/m/d');
    }

    public function getAvatarColorAttribute(): string
    {
        $colors = [
            'A' => '#8F7F70',
            'B' => '#6B7280',
            'C' => '#7C6F9F',
            'D' => '#5F8D8A',
            'E' => '#B7791F',
            'F' => '#A66B6B',
            'G' => '#5B7C99',
            'H' => '#7A8B5B',
            'I' => '#9A6B8A',
            'J' => '#6C7A89',
            'K' => '#A67C52',
            'L' => '#64748B',
            'M' => '#8B6F9C',
            'N' => '#5C8D89',
            'O' => '#A35D6A',
            'P' => '#6D7F5E',
            'Q' => '#8A718D',
            'R' => '#5E7894',
            'S' => '#9B7653',
            'T' => '#657A73',
            'U' => '#8B6578',
            'V' => '#607D8B',
            'W' => '#806A5B',
            'X' => '#6E7891',
            'Y' => '#92705F',
            'Z' => '#66785F',
        ];
        $firstLetter = strtoupper(mb_substr($this->username, 0, 1));

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
