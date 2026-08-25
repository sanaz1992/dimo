<?php

namespace Modules\Instagram\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Filterable;
use Modules\Instagram\Enums\MessageDirection;
use Modules\Instagram\Enums\MessageType;

class Message extends Model
{
    use Filterable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'conversation_id',
        'instagram_message_id',
        'sender_ig_id',
        'recipient_ig_id',
        'direction',
        'type',
        'message_body',
        'payload',
        'sent_at',
    ];

    protected $casts = [
        'direction' => MessageDirection::class,
        'type' => MessageType::class,
        'sent_at' => 'datetime',
    ];

    public function getSentAtJalaliAttribute()
    {
        return verta($this->sent_at)->format('Y/m/d H:i:s');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
