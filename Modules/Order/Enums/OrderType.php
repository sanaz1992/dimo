<?php

namespace Modules\Order\Enums;

enum OrderType: string
{
    case INTERNAL = 'internal';
    case SALE = 'sale';
    case MANUAL = 'manual';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::INTERNAL->value => 'داخلی',
            self::SALE->value => 'فروش',
            self::MANUAL->value => 'دستی',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public static function colors(): array
    {
        return [
            self::INTERNAL->value => 'bg-purple-50 text-purple-700 inset-ring-purple-500/10',
            self::SALE->value => 'bg-green-50 text-green-700 inset-ring-green-600/20',
            self::MANUAL->value => 'bg-gray-50 text-gray-700 inset-ring-gray-500/10',
        ];
    }

    public function color(): string
    {
        return self::colors()[$this->value];
    }
}
