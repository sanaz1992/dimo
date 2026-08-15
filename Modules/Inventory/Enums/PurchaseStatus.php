<?php

namespace Modules\Inventory\Enums;

enum PurchaseStatus: string
{
    case DRAFT = 'draft';
    case RECEIVED = 'received';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::DRAFT->value => 'پیش‌نویس',
            self::RECEIVED->value => 'دریافت شده',
            self::CANCELLED->value => 'لغو شده',
        ];
    }
}
