<?php

namespace Modules\Order\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case AWAITING_PAYMENT = 'awaiting_payment';
    case PAID = 'paid';
    case AWAITING_MANAGER_APPROVAL = 'awaiting_manager_approval';
    case PROCESSING = 'processing';
    case PAYMENT_FAILED = 'payment_failed';
    case EXPIRED = 'expired';
    case MANUAL_REVIEW = 'manual_review';
    case COMPLETED = 'campleted';
    case CANCELED = 'canceled';
    case AWAITING_SHIPPED = 'awaiting_shipped';
    case SHIPPED = 'shipped';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'پیش نویس',
            self::AWAITING_PAYMENT => 'در انتظار پرداخت',
            self::PAID => 'پرداخت شده',
            self::AWAITING_MANAGER_APPROVAL => 'در انتظار تایید مدیر',
            self::PROCESSING => 'در حال پردازش',
            self::PAYMENT_FAILED => 'ناموفق',
            self::EXPIRED => 'منقضی شده',
            self::MANUAL_REVIEW => 'نیاز به بررسی',
            self::COMPLETED => 'تکمیل شده',
            self::CANCELED => 'لغو شده',
            self::AWAITING_SHIPPED => 'در انتظار ارسال',
            self::SHIPPED => 'ارسال شده',
        };
    }
}
