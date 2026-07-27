<?php

namespace Modules\Transactions\Services;

use InvalidArgumentException;
use Modules\Transactions\Contracts\PaymentGatewayInterface;
use Modules\Transactions\Services\Gateways\ZarinpalGateway;

class PaymentGatewayManager
{
    public function gateway(?string $driver = null): PaymentGatewayInterface
    {
        $driver ??= config('transactions.payment.default_gateway');

        return match ($driver) {
            'zarinpal' => app(ZarinpalGateway::class),
            // 'idpay' => app(IdPayGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway [{$driver}]."),
        };
    }
}
