<?php

namespace Modules\Core\Helpers;

use Carbon\Carbon;
use Morilog\Jalali\CalendarUtils;
use Throwable;

class ConvertDatesHelper
{
    public static function jalaliToGregorian($datetime, string $format = 'Y/m/d H:i:s')
    {
        if (!$datetime) return null;
        try {
            $datetime = CalendarUtils::createDateTimeFromFormat('Y/m/d', self::convertPersianNumbersToEnglish($datetime))
                ->format($format);
            return $datetime;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function jalaliToGregorianWithTime($datetime, string $format = 'Y/m/d H:i:s')
    {
        try {
            $datetime = str_replace('ساعت ', '', $datetime);
            $datetime = self::convertPersianNumbersToEnglish($datetime);
            $datetime = Carbon::parse($datetime)->toDateTimeString();
            $datetime = CalendarUtils::createDateTimeFromFormat('Y-m-d H:i:s', $datetime)
                ->format($format);

            // $datetime=verta($datetime)->format('Y/m/d H:i:s');
            // return verta($datetime)->toCarbon();
            return $datetime;
        } catch (Throwable $e) {
            dd($e);
            return null;
        }
    }

    public static function convertPersianNumbersToEnglish($input)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '٤', '۵', '٥', '٦', '۶', '۷', '۸', '۹'];
        $english = [0, 1, 2, 3, 4, 4, 5, 5, 6, 6, 7, 8, 9];
        return str_replace($persian, $english, $input);
    }
}
