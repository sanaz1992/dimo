<?php

namespace Modules\Instagram\Enums;

enum ConversationStatus: string
{
    case OPEN = 'open';
    case CLOSE = 'close';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::OPEN->value => 'باز',
            self::CLOSE->value => 'بسته شده',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'green',
            self::CLOSE => 'red',
        };
    }
}
