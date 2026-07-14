<?php

namespace Modules\Product\Enums;

enum ProductOrderType: string
{
    case LEATHER = 'leather';
    case BASE = 'base';



    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::LEATHER->value  => 'چرم',
            self::BASE->value  => 'زیرکار',

        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }



}
