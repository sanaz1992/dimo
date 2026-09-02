<?php

namespace Modules\Instagram\Enums;

enum InstagramMediaType: string
{
    case FEED = 'FEED';
    case REELS = 'REELS';
    case STORY = 'STORY';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::FEED->value => 'feed',
            self::REELS->value => 'reels',
            self::STORY->value => 'story',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::FEED => 'slate',
            self::REELS => 'emerald',
            self::STORY => 'amber',
        };
    }
}
