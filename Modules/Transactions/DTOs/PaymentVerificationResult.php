<?php

namespace Modules\Transactions\DTOs;

class PaymentVerificationResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $referenceId = null,
        public readonly ?string $authority = null,
        public readonly ?string $message = null,
        public readonly array $raw = [],
    ) {}
}
