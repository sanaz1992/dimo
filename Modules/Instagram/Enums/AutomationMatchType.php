<?php

namespace Modules\Instagram\Enums;

enum AutomationMatchType: string
{
    case EXACT = 'exact';
    case CONTAINS = 'contains';
    case STARTS_WITH = 'starts_with';
    case ENDS_WITH = 'ends_with';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::EXACT->value => 'دقیق',
            self::CONTAINS->value => 'حاوی',
            self::STARTS_WITH->value => 'شروع با',
            self::ENDS_WITH->value => 'پایان با',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::EXACT => 'green',
            self::CONTAINS => 'blue',
            self::STARTS_WITH => 'rose',
            self::ENDS_WITH => 'amber',
        };
    }
}
