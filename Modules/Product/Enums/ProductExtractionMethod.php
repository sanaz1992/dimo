<?php

namespace Modules\Product\Enums;

enum ProductExtractionMethod: string
{
    case TRADITIONAL = 'traditional';
    case INDUSTRIAL = 'Industrial ';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::TRADITIONAL->value => 'سنتی',
            self::INDUSTRIAL->value => 'صنعتی',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
