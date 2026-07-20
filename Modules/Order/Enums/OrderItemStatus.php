<?php

namespace Modules\Order\Enums;

enum OrderItemStatus: string
{
    case DRAFT = 'draft';
    case AWAITING_SALES_MANAGER_APPROVAL = 'awaiting_sales_manager_approval';
    case APPROVED = 'approved';
    case CANCELED = 'canceled';
    case IN_PRODUCTION = 'in_production';
    case PAUSED = 'paused';
    case PRODUCED = 'produced';
    case QUALITY_APPROVED = 'quality_approved';
    case QUALITY_REJECTED = 'quality_rejected';
    case PACKAGED = 'packaged';
    case AWAITING_SHIPPED = 'awaiting_shipped';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case RETURNED_TO_PRODUCTION = 'returned_to_production';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::DRAFT->value => 'پیش نویس',
            self::AWAITING_SALES_MANAGER_APPROVAL->value => 'در انتظار تایید مدیر فروش',
            self::APPROVED->value => 'تایید مدیر فروش',
            self::CANCELED->value => 'لغو شده',
            self::IN_PRODUCTION->value => 'در حال تولید',
            self::PAUSED->value => 'متوقف شده',
            self::PRODUCED->value => 'تولید شده',
            self::QUALITY_APPROVED->value => 'تأیید شده در کنترل کیفیت',
            self::QUALITY_REJECTED->value => 'رد شده در کنترل کیفیت',
            self::PACKAGED->value => 'بسته بندی شده',
            self::AWAITING_SHIPPED->value => 'منتظر ارسال',
            self::SHIPPED->value => 'ارسال شده',
            self::DELIVERED->value => 'تحویل داده شده',
            self::RETURNED_TO_PRODUCTION->value => 'بازگشت به تولید',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public static function colors(): array
    {
        return [
            self::DRAFT->value => 'bg-gray-50 text-gray-600 inset-ring-gray-500/10',
            self::AWAITING_SALES_MANAGER_APPROVAL->value => ' bg-[#FEFEE8] text-[#9E700A] inset-ring-yellow-600/20',
            self::APPROVED->value => 'bg-green-50 text-green-700 inset-ring-green-600/20',
            self::CANCELED->value => 'bg-red-50 text-red-600 inset-ring-red-600/10',
            self::IN_PRODUCTION->value => 'bg-blue-50 text-blue-700 inset-ring-blue-700/10',
            self::PAUSED->value => ' bg-[#FEFEE8] text-[#9E700A] inset-ring-yellow-600/20',
            self::PRODUCED->value => 'bg-green-50 text-green-700 inset-ring-green-600/20',
            self::QUALITY_APPROVED->value => 'bg-green-50 text-green-700 inset-ring-green-600/20',
            self::QUALITY_REJECTED->value => 'bg-red-50 text-red-600 inset-ring-red-600/10',
            self::PACKAGED->value => 'bg-[#F9F6ED] text-primary inset-ring-purple-700/10',
            self::AWAITING_SHIPPED->value => 'bg-[#F9F6ED] text-primary inset-ring-purple-700/10',
            self::SHIPPED->value => ' bg-[#FEFEE8] text-[#9E700A] inset-ring-yellow-600/20',
            self::DELIVERED->value => 'bg-green-50 text-green-700 inset-ring-green-600/20',
            self::RETURNED_TO_PRODUCTION->value => 'bg-[#FEFEE8] text-[#9E700A] inset-ring-yellow-600/20',
        ];
    }

    public function color(): string
    {
        return self::colors()[$this->value];
    }
}
