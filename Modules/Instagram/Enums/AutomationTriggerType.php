<?php

namespace Modules\Instagram\Enums;

enum AutomationTriggerType: string
{
    case COMMENT = 'comment';
    case MESSAGE = 'message';
    case MENTION = 'mention';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::COMMENT->value => 'کامنت',
            self::MESSAGE->value => 'پیام',
            self::MENTION->value => 'منشن',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::COMMENT => 'amber',
            self::MESSAGE => 'blue',
            self::MENTION => 'rose',
        };
    }
}
