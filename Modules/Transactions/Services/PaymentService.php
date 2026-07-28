<?php

namespace Modules\Transactions\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Helpers\SettingHelper;
use Modules\Order\Entities\Order;
use Modules\Order\Services\OrderService;
use Modules\Transactions\DTOs\PaymentRequestData;
use Modules\Transactions\DTOs\PaymentVerificationResult;
use Modules\Transactions\Entities\Transaction;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected PaymentGatewayManager $gatewayManager,
    ) {}

    public function pay(int $orderId, ?string $driver = null): string
    {
        $order = resolve(OrderService::class)->find($orderId);
        $gateway = $this->gatewayManager->gateway($driver);

        $callbackUrl = route('transactions.callback', [
            'order' => $order,
            'gateway' => $gateway->name(),
        ]);

        // $settingHelper = app(SettingHelper::class);
        // $currency = $settingHelper->currency();
        $amount = (int) $order->total_amount;
        // if ($currency == "rial") {
        //     $amount /= 10;
        // }
        $requestData = new PaymentRequestData(
            amount: $amount,
            callbackUrl: $callbackUrl,
            description: 'Payment for order #'.$order->id,
            mobile: optional($order->user)->mobile,
            email: optional($order->user)->email,
        );

        $requestResult = $gateway->request($order, $requestData);

        DB::transaction(function () use ($order, $gateway, $requestData, $requestResult) {
            Transaction::create([
                'order_id' => $order->id,
                'gateway' => $gateway->name(),
                'amount' => $requestData->amount,
                'status' => 'pending',
                'payload' => [
                    'request' => [
                        'token' => $requestResult->token,
                        'authority' => $requestResult->authority,
                        'payment_url' => $requestResult->paymentUrl,
                        'response' => $requestResult->raw,
                    ],
                ],
            ]);

            // $order->update([
            //     'payment_status' => 'pending',
            // ]);
        });

        return $gateway->redirectUrl($requestResult);
    }

    public function verify(Order $order, array $callbackData, ?string $driver = null): PaymentVerificationResult
    {
        $gateway = $this->gatewayManager->gateway($driver);
        $result = $gateway->verify($order, $callbackData);

        DB::transaction(function () use ($order, $gateway, $callbackData, $result) {
            $transaction = Transaction::query()
                ->where('order_id', $order->id)
                ->where('gateway', $gateway->name())
                ->where('payload->request->authority', $result->authority)
                ->latest('id')
                ->first();

            if (! $transaction) {
                $transaction = Transaction::query()
                    ->where('order_id', $order->id)
                    ->where('gateway', $gateway->name())
                    ->where('status', 'pending')
                    ->latest('id')
                    ->first();
            }

            if (! $transaction) {
                throw new RuntimeException('Transaction not found.');
            }

            $payload = $transaction->payload ?? [];

            $payload['callback'] = [
                'data' => $callbackData,
            ];

            $payload['verify'] = [
                'ref_id' => $result->referenceId,
                'response' => $result->raw,
                'message' => $result->message,
            ];

            $transaction->update([
                'status' => $result->success ? 'paid' : 'failed',
                'reference_id' => $result->referenceId,
                'payload' => $payload,
                'authority' => $result->authority,
            ]);

            $order->update([
                'status' => $result->success ? 'paid' : $result->success,
                'payment_status' => $result->success ? 'paid' : 'failed',
            ]);

        });

        return $result;

    }
}
