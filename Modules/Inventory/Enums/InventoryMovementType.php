<?php

namespace Modules\Inventory\Enums;

enum InventoryMovementType: string
{
    case RESERVE = 'reserve';

    case RELEASE = 'release';

    case CONVERT = 'convert';

    case ADJUST = 'adjust';

    case REFUND = 'refund';

    public function label(): string
    {
        return match ($this) {
            self::RESERVE => 'رزرو',
            self::RELEASE => 'RELEASE',
            self::CONVERT => 'CONVERT',
            self::ADJUST => 'ADJUST',
            self::REFUND => 'REFUND',
        };
    }
}
