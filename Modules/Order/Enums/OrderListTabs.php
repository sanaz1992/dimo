<?php

namespace Modules\Order\Enums;

enum OrderListTabs: string
{
    case DRAFT = 'draft';
    case All = 'all';
    case PENDING = 'pending';
    case AWAITING_PRODUCTION = 'awaiting_production';
    case IN_PRODUCTION = 'in_production';
    case PRODUCED = 'produced';
    case CANCELED = 'canceled';
    case DELIVERED = 'delivered';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::DRAFT->value   => 'سفارشات پیش نویس',
            self::All->value => 'همه سفارشات',
            self::PENDING->value   => 'سفارشات در انتظار بررسی',
            self::AWAITING_PRODUCTION->value   => 'سفارشات در انتظار تولید',
            self::IN_PRODUCTION->value => 'سفارشات در جریان تولید',
            self::PRODUCED->value => 'سفارشات انبار و تولید شده',
            self::CANCELED->value => 'سفارشات لغو شده',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public static function statuses(): array
    {
        return [
            self::DRAFT->value   => [OrderStatus::DRAFT->value],
            self::All->value => [OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value, OrderStatus::APPROVED->value, OrderStatus::IN_PRODUCTION->value, OrderStatus::PAUSED->value, OrderStatus::PRODUCED->value, OrderStatus::PACKAGING->value, OrderStatus::AWAITING_SHIPPED->value, OrderStatus::SHIPPED->value,  OrderStatus::CANCELED->value],
            self::PENDING->value  => [OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value],
            self::AWAITING_PRODUCTION->value => [OrderStatus::APPROVED->value],
            self::IN_PRODUCTION->value =>  [OrderStatus::IN_PRODUCTION->value, OrderStatus::PAUSED->value],
            self::PRODUCED->value =>  [OrderStatus::PRODUCED->value, OrderStatus::PACKAGING->value, OrderStatus::AWAITING_SHIPPED->value, OrderStatus::SHIPPED->value, OrderStatus::DELIVERED->value],
            self::CANCELED->value => [OrderStatus::CANCELED->value]
        ];
    }
    public function status(): string
    {
        return self::statuses()[$this->value];
    }

    public static function seller_statuses(): array
    {
        return [
            self::DRAFT->value   => [OrderStatus::DRAFT->value],
            self::All->value => [OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value, OrderStatus::APPROVED->value, OrderStatus::IN_PRODUCTION->value, OrderStatus::PAUSED->value, OrderStatus::PRODUCED->value, OrderStatus::PACKAGING->value, OrderStatus::AWAITING_SHIPPED->value, OrderStatus::SHIPPED->value, OrderStatus::DELIVERED->value, OrderStatus::CANCELED->value],
            self::PENDING->value  => [OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value, OrderStatus::APPROVED->value],
            // self::AWAITING_PRODUCTION->value => [],
            self::IN_PRODUCTION->value =>  [OrderStatus::IN_PRODUCTION->value, OrderStatus::PAUSED->value],
            self::PRODUCED->value =>  [OrderStatus::PRODUCED->value, OrderStatus::PACKAGING->value, OrderStatus::AWAITING_SHIPPED->value, OrderStatus::SHIPPED->value],
            self::DELIVERED->value => [OrderStatus::DELIVERED->value],
            self::CANCELED->value => [OrderStatus::CANCELED->value]
        ];
    }
}
