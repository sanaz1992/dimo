<?php

namespace Modules\Transactions\DTOs;

class PaymentRequestData
{
    public function __construct(
        public readonly int $amount,
        public readonly string $callbackUrl,
        public readonly string $description,
        public readonly ?string $mobile = null,
        public readonly ?string $email = null,
    ) {}
}
