<?php

namespace Modules\Product\Enums;

enum ProductPackagingType: string
{
    case GLASS_BOTTLE = 'glass_Bottle';
    case PET_BOTTLE  = 'pet_bottle ';
    case GALLON_JUG = 'gallon_jug';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::GLASS_BOTTLE->value  => 'شیشه‌ای',
            self::PET_BOTTLE->value  => 'پلاستیکی',
            self::GALLON_JUG->value  => 'گالن',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
