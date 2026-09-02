<?php

namespace Modules\Instagram\Enums;

enum AutomationActionType: string
{
    case SEND_MESSAGE = 'send_message';
    case ADD_TAG = 'add_tag';
    case SEND_EMAIL = 'send_email';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::SEND_MESSAGE->value => 'ارسال پیام',
            self::ADD_TAG->value => 'افزودن برچسب',
            self::SEND_EMAIL->value => 'ارسال ایمیل',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::SEND_MESSAGE => 'green',
            self::ADD_TAG => 'blue',
            self::SEND_EMAIL => 'rose',
        };
    }
}
