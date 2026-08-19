<?php

namespace Modules\Tenant\Enums;

enum TenantStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::ACTIVE->value => 'فعال',
            self::SUSPENDED->value => 'تعلیق‌شده',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'violet',
            self::SUSPENDED => 'slate',
        };
    }
}
