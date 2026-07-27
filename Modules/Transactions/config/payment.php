<?php

return [
    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'zarinpal'),

    'gateways' => [
        'zarinpal' => [
            'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
            'request_url' => env('ZARINPAL_REQUEST_URL'),
            'verify_url' => env('ZARINPAL_VERIFY_URL'),
            'start_pay_url' => env('ZARINPAL_START_PAY_URL'),
        ],

        'idpay' => [
            'api_key' => env('IDPAY_API_KEY'),
            'request_url' => env('IDPAY_REQUEST_URL'),
            'verify_url' => env('IDPAY_VERIFY_URL'),
            'start_pay_url' => env('IDPAY_START_PAY_URL'),
        ],
    ],
];
