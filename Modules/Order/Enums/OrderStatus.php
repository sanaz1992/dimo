<?php

namespace Modules\Order\Enums;

enum OrderStatus
{
    public const DRAFT = 'draft';

    public const AWAITING_PAYMENT = 'awaiting_payment';

    public const PAID = 'paid';

    public const PROCESSING = 'processing';

    public const PAYMENT_FAILED = 'payment_failed';

    public const EXPIRED = 'expired';

    public const MANUAL_REVIEW = 'manual_review';
}
