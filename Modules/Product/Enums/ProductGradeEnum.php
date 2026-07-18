<?php

namespace Modules\Product\Enums;

enum ProductGradeEnum: string
{
    case DOUBLE_DISTILLED = 'double_distilled';
    case PREMIUM  = 'premium ';
    case NORMAL = 'normal';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::DOUBLE_DISTILLED->value  => 'دوآتیشه',
            self::PREMIUM->value  => 'ممتاز',
            self::NORMAL->value  => 'معمولی',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
