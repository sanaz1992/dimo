<?php

namespace Modules\Transactions\Contracts;

use Modules\Order\Entities\Order;
use Modules\Transactions\DTOs\PaymentRequestData;
use Modules\Transactions\DTOs\PaymentRequestResult;
use Modules\Transactions\DTOs\PaymentVerificationResult;

interface PaymentGatewayInterface
{
    public function request(Order $order, PaymentRequestData $data): PaymentRequestResult;

    public function redirectUrl(PaymentRequestResult $result): string;

    public function verify(Order $order, array $callbackData): PaymentVerificationResult;

    public function name(): string;
}
