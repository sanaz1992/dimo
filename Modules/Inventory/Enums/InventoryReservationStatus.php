<?php

namespace Modules\Inventory\Enums;

enum InventoryReservationStatus: string
{
    case ACTIVE = 'active';
    case RELEASED = 'released';
    case CONVERTED = 'converted';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'فعال',
            self::RELEASED => 'RELEASED',
            self::CONVERTED => 'CONVERTED',
            self::EXPIRED => 'منقضی شده',

        };
    }
}
