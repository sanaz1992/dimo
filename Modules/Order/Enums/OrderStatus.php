<?php

namespace Modules\Order\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case AWAITING_SALES_MANAGER_APPROVAL = 'awaiting_sales_manager_approval';
    case APPROVED = 'approved';
    case CANCELED = 'canceled';
    case IN_PRODUCTION = 'in_production';
    case PAUSED = 'paused';
    case PRODUCED = 'produced';
    case PACKAGING = 'packaging';
    case AWAITING_SHIPPED = 'awaiting_shipped';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

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
            self::PACKAGING->value => 'در حال بسته بندی',
            self::AWAITING_SHIPPED->value => 'منتظر ارسال',
            self::SHIPPED->value => 'ارسال شده',
            self::DELIVERED->value => 'تحویل داده شده',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public static function colors(): array
    {
        return [
            self::DRAFT->value => 'bg-purple-50 text-purple-700 inset-ring-purple-500/10',
            self::AWAITING_SALES_MANAGER_APPROVAL->value => 'bg-yellow-50 text-yellow-700 inset-ring-yellow-600/20',
            self::APPROVED->value => 'bg-green-50 text-green-700 inset-ring-green-600/20',
            self::CANCELED->value => 'bg-red-50 text-red-700 inset-ring-red-600/10',
            self::IN_PRODUCTION->value => 'bg-blue-50 text-blue-700 inset-ring-blue-700/10',
            self::PAUSED->value => 'bg-orange-50 text-orange-700 inset-ring-orange-600/20',
            self::PRODUCED->value => 'bg-cyan-50 text-cyan-700 inset-ring-cyan-600/20',
            self::PACKAGING->value => 'bg-indigo-50 text-indigo-700 inset-ring-indigo-700/10',
            self::AWAITING_SHIPPED->value => 'bg-pink-50 text-pink-700 inset-ring-pink-700/10',
            self::SHIPPED->value => 'bg-sky-50 text-sky-700 inset-ring-sky-600/20',
            self::DELIVERED->value => 'bg-emerald-50 text-emerald-700 inset-ring-emerald-600/20',
        ];
    }

    public function color(): string
    {
        return self::colors()[$this->value];
    }

    public static function activeStatuses(): array
    {
        return [
            self::AWAITING_SALES_MANAGER_APPROVAL->value,
            self::APPROVED->value,
            self::IN_PRODUCTION->value,
            self::PAUSED->value,
            self::PRODUCED->value,
            self::PACKAGING->value,
            self::AWAITING_SHIPPED->value,
            self::SHIPPED->value,
        ];
    }
}
