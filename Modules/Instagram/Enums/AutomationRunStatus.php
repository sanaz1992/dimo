<?php

namespace Modules\Instagram\Enums;

enum AutomationRunStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case SKIPPED = 'skipped';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::PENDING->value => 'در انتظار',
            self::PROCESSING->value => 'در حال پردازش',
            self::COMPLETED->value => 'تکمیل شده',
            self::FAILED->value => 'شکست خورده',
            self::SKIPPED->value => 'رد شده',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::PROCESSING => 'yellow',
            self::COMPLETED => 'green',
            self::FAILED => 'red',
            self::SKIPPED => 'orange',
        };
    }
}
