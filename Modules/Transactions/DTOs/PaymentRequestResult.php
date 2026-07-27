<?php

namespace Modules\Transactions\DTOs;

class PaymentRequestResult
{
    public function __construct(
        public readonly string $token,
        public readonly ?string $authority = null,
        public readonly ?string $paymentUrl = null,
        public readonly array $raw = [],
    ) {}
}
