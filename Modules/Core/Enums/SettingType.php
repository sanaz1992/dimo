<?php

namespace Modules\Core\Enums;

enum SettingType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case IMAGE = 'image';
    case BOOL = 'bool';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::TEXTAREA->value => 'متن',
            self::TEXT->value => 'عنوان',
            self::IMAGE->value => 'تصویر',
            self::BOOL->value => 'بله/خیر',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
