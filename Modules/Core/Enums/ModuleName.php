<?php

namespace Modules\Core\Enums;

enum ModuleName: string
{
    case MARKETING = 'marketing';
    case SALE = 'sale';

    // متدی برای گرفتن تمام مقادیر جهت ولیدیشن
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
