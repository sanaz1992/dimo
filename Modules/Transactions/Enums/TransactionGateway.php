<?php

namespace Modules\Transactions\Enums;

enum TransactionGateway: string
{
    case ZARINPAL = 'zarinpal';

    public function label(): string
    {
        return match ($this) {
            self::ZARINPAL => 'زرین پال',
        };
    }
}
