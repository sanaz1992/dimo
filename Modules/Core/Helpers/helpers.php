<?php

if (! function_exists('toPersianNumber')) {
    function toPersianNumber(int|float|string|null $value): string
    {
        return strtr((string) $value, [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹',

            // Arabic digits
            '٠' => '۰',
            '١' => '۱',
            '٢' => '۲',
            '٣' => '۳',
            '٤' => '۴',
            '٥' => '۵',
            '٦' => '۶',
            '٧' => '۷',
            '٨' => '۸',
            '٩' => '۹',
        ]);
    }
}

if (! function_exists('formatPrice')) {
    function formatPrice(int|float|string|null $value): string
    {
        return toPersianNumber(number_format((float) $value));
    }
}
