<?php

namespace Modules\Instagram\Enums;

enum WebhookEventStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case FAILED = 'failed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::PENDING->value => 'در انتظار اجرا',
            self::PROCESSING->value => 'در حال اجرا',
            self::PROCESSED->value => 'اجرا شده',
            self::FAILED->value => 'خطا داده',
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
            self::PROCESSING => 'slate',
            self::PROCESSED => 'green',
            self::FAILED => 'red',
        };
    }
}
