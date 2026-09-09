<?php

namespace Modules\Instagram\Enums;

enum MessageSource: string
{
    case WEBHOOK = 'webhook';
    case MANUAL = 'manual';
    case AUTOMATION = 'automation';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::WEBHOOK->value => 'کاربر پیام داده',
            self::MANUAL->value => 'ادمین از پنل فرستاده',
            self::AUTOMATION->value => 'سیستم/Automation فرستاده',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::WEBHOOK => 'green',
            self::MANUAL => 'blue',
            self::AUTOMATION => 'yellow',
        };
    }
}
