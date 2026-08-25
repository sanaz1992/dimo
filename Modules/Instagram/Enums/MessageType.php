<?php

namespace Modules\Instagram\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case STICKER = 'sticker';
    case UNKNOWN = 'unknown';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::TEXT->value => 'متن',
            self::IMAGE->value => 'تصویر',
            self::VIDEO->value => 'ویدئو',
            self::AUDIO->value => 'صدا',
            self::STICKER->value => 'استیکر',
            self::UNKNOWN->value => 'نامشخص',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::TEXT => 'blue',
            self::IMAGE => 'blue',
            self::VIDEO => 'blue',
            self::AUDIO => 'blue',
            self::STICKER => 'blue',
            self::UNKNOWN => 'blue',
        };
    }
}
