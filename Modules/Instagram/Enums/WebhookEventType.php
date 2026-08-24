<?php

namespace Modules\Instagram\Enums;

enum WebhookEventType: string
{
    case MESSAGE_EDIT = 'message_edit';
    case MESSAGE_ECHO = 'message_echo';
    case MESSAGE = 'message';
    case READ = 'read';
    case POSTBACK = 'postback';
    case UNKNOWN = 'unknown';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::MESSAGE_EDIT->value => 'ویرایش پیام',
            self::MESSAGE_ECHO->value => 'پیام خروجی',
            self::MESSAGE->value => 'پیام ورودی',
            self::READ->value => 'خوانده‌شدن پیام',
            self::POSTBACK->value => 'پست‌بک',
            self::UNKNOWN->value => 'ناشناخته',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    // public function color(): string
    // {
    //     return match ($this) {
    //         self::PENDING => 'amber',
    //         self::PROCESSING => 'slate',
    //         self::PROCESSED => 'green',
    //         self::FAILED => 'red',
    //     };
    // }
}
