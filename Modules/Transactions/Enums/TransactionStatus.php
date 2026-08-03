<?php

namespace Modules\Transactions\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'پیش نویس',
            self::PAID => 'پرداخت شده',
            self::FAILED => 'ناموفق',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::PAID => 'emerald',
            self::FAILED => 'red',
        };
    }
}
