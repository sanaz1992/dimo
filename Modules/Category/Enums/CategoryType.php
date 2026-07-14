<?php

namespace Modules\Category\Enums;

enum CategoryType: string
{
    case PRODUCT = 'product';
    case RAW_MATERIAL = 'raw_material';



    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::PRODUCT->value  => 'محصول',
            self::RAW_MATERIAL->value  => 'مواد اولیه',

        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }



}
