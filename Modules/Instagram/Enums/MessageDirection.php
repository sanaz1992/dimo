<?php

namespace Modules\Instagram\Enums;

enum MessageDirection: string
{
    case INCOMING = 'incoming';
    case OUTGOING = 'outgoing';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::INCOMING->value => 'ورودی',
            self::OUTGOING->value => 'خروجی',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::INCOMING => 'green',
            self::OUTGOING => 'blue',
        };
    }
}
