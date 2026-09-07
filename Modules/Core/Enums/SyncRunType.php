<?php

namespace Modules\Core\Enums;

enum SyncRunType: string
{
    case INSTAGRAM_POSTS = 'instagram_posts';
    case INSTAGRAM_COMMENTS = 'instagram_comments';
    case INSTAGRAM_CONVERSATIONS = 'instagram_conversations';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::INSTAGRAM_POSTS->value => 'پست های اینستاگرام',
            self::INSTAGRAM_COMMENTS->value => 'کامنت های اینستاگرام',
            self::INSTAGRAM_CONVERSATIONS->value => 'مکالکات اینستاگرام',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::INSTAGRAM_POSTS => 'amber',
            self::INSTAGRAM_COMMENTS => 'blue',
            self::INSTAGRAM_CONVERSATIONS => 'green'
        };
    }
}
