<?php

namespace Modules\Instagram\Enums;

enum InstagramAccountStatus: string
{
    case CONNECTED = 'connected';
    case EXPIRED = 'expired';
    case REVOKED = 'revoked';
    case ERROR = 'error';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::CONNECTED->value => 'متصل شده',
            self::EXPIRED->value => 'منقضی شده',
            self::REVOKED->value => 'باطل شده',
            self::ERROR->value => 'خطا داده',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::CONNECTED => 'violet',
            self::EXPIRED => 'blue',
            self::REVOKED => 'amber',
            self::ERROR => 'slate',
        };
    }
}
