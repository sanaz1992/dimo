<?php

namespace Modules\Core\Enums;

enum CurrencyEnum: string
{
    case RIAL   = 'rial';
    case TOMAN  = 'toman';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::RIAL->value   => 'ریال',
            self::TOMAN->value  => 'تومان',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
