<?php

namespace Modules\Core\Enums;

enum LocalEnum: string
{
    case fa_IR = 'fa_IR';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::fa_IR->value => 'فارسی — ایران',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
