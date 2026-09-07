<?php

namespace Modules\Core\Enums;

enum SyncRunStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::PENDING->value => 'در انتظار',
            self::RUNNING->value => 'در حال اجرا',
            self::COMPLETED->value => 'تکمیل شده',
            self::FAILED->value => 'ناموفق',
            self::CANCELLED->value => 'لغو شده',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'amber',
            self::RUNNING => 'blue',
            self::COMPLETED => 'green',
            self::FAILED => 'rose',
            self::CANCELLED => 'slate',
        };
    }
}
