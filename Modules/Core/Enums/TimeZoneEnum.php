<?php

namespace Modules\Core\Enums;

enum TimeZoneEnum: string
{
    case TEHRAN = 'Asia/Tehran';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::TEHRAN->value => 'تهران — UTC+03:30',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
