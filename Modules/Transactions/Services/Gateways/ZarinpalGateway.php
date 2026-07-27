<?php

namespace Modules\Transactions\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Modules\Order\Entities\Order;
use Modules\Transactions\Contracts\PaymentGatewayInterface;
use Modules\Transactions\DTOs\PaymentRequestData;
use Modules\Transactions\DTOs\PaymentRequestResult;
use Modules\Transactions\DTOs\PaymentVerificationResult;
use RuntimeException;

class ZarinpalGateway implements PaymentGatewayInterface
{
    public function request(Order $order, PaymentRequestData $data): PaymentRequestResult
    {
        $merchantId = config('transactions.payment.gateways.zarinpal.merchant_id');
        $requestUrl = config('transactions.payment.gateways.zarinpal.request_url');

        $payload = [
            'merchant_id' => $merchantId,
            'amount' => (int) $data->amount,
            'callback_url' => $data->callbackUrl,
            'referrer_id' => $order->id,
            'description' => $data->description,
            'metadata' => array_filter([
                'mobile' => $data->mobile,
                'email' => $data->email,
            ]),
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $requestUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($response === false || ! empty($error)) {
            throw new RuntimeException(
                'ارتباط با درگاه پرداخت ناموفق بود. curl_error='.$error
            );
        }

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException(
                'درخواست به زرین‌پال ناموفق بود. http_status='.$status.' body='.$response
            );
        }

        $body = json_decode($response, true);

        if (! is_array($body)) {
            throw new RuntimeException(
                'پاسخ دریافتی از زرین‌پال معتبر نیست. body='.$response
            );
        }

        $authority = data_get($body, 'data.authority');
        $code = (int) data_get($body, 'data.code', 0);
        $message = data_get($body, 'data.message')
            ?? data_get($body, 'errors.message')
            ?? 'خطای نامشخص از زرین‌پال';

        if ($code !== 100 || ! $authority) {
            throw new RuntimeException(
                'توکن پرداخت از زرین‌پال دریافت نشد. code='.$code.' message='.$message.' body='.json_encode($body, JSON_UNESCAPED_UNICODE)
            );
        }

        return new PaymentRequestResult(
            token: $authority,
            authority: $authority,
            paymentUrl: rtrim(config('transactions.payment.gateways.zarinpal.start_pay_url'), '/').'/'.$authority,
            raw: $body,
        );
    }

    public function redirectUrl(PaymentRequestResult $result): string
    {
        if ($result->paymentUrl) {
            return $result->paymentUrl;
        }

        return rtrim(config('transactions.payment.gateways.zarinpal.start_pay_url'), '/').'/'.$result->token;
    }

    public function verify(Order $order, array $callbackData): PaymentVerificationResult
    {
        $status = $callbackData['Status'] ?? null;
        $authority = $callbackData['Authority'] ?? null;

        if (! $authority) {
            return new PaymentVerificationResult(
                success: false,
                authority: null,
                message: 'کد پیگیری پرداخت از درگاه دریافت نشد.',
                raw: $callbackData,
            );
        }

        if ($status !== 'OK') {
            return new PaymentVerificationResult(
                success: false,
                authority: $authority,
                message: 'پرداخت توسط کاربر لغو شد یا ناموفق بود.',
                raw: $callbackData,
            );
        }

        $response = Http::post(config('transactions.payment.gateways.zarinpal.verify_url'), [
            'merchant_id' => config('transactions.payment.gateways.zarinpal.merchant_id'),
            'amount' => (int) $order->total_amount,
            'authority' => $authority,
        ]);

        if (! $response->successful()) {
            return new PaymentVerificationResult(
                success: false,
                authority: $authority,
                message: 'درخواست تایید پرداخت به درگاه ناموفق بود.',
                raw: $response->json() ?? [],
            );
        }

        $body = $response->json();

        $code = (int) data_get($body, 'data.code', 0);
        $refId = data_get($body, 'data.ref_id');
        $message = data_get($body, 'data.message') ?? data_get($body, 'errors.message');

        return new PaymentVerificationResult(
            success: in_array($code, [100, 101], true),
            referenceId: $refId ? (string) $refId : null,
            authority: $authority,
            message: $this->resolveVerifyMessage($code, $message),
            raw: $body,
        );
    }

    public function name(): string
    {
        return 'zarinpal';
    }

    protected function resolveVerifyMessage(int $code, ?string $defaultMessage = null): string
    {
        return match ($code) {
            100 => 'پرداخت با موفقیت انجام شد.',
            101 => 'این تراکنش قبلا با موفقیت تایید شده است.',
            default => $defaultMessage ?: 'پرداخت ناموفق بود.',
        };
    }
}
