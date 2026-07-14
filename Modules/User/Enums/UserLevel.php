<?php

namespace Modules\User\Enums;

enum UserLevel: string
{
    case USER   = 'user';
    case ADMIN  = 'admin';
    case SALES_OPERATOR = 'sales_operator';
    case DRIVER = 'driver';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::USER->value   => 'کاربر معمولی',
            self::ADMIN->value  => 'مدیر سیستم',
            self::SALES_OPERATOR->value => 'کارشناس فروش',
            self::DRIVER->value => 'راننده',
        ];
    }

    public static function adminLabels(): array
    {
        return [
            self::ADMIN->value  => 'مدیر سیستم',
            self::SALES_OPERATOR->value => 'کارشناس فروش',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
