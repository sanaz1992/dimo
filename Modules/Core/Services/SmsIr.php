<?php

namespace Modules\Core\Services;

use Exception;

class SmsIr
{
    /**
     * @param  array  $mobiles
     * @param  int  $userFormUrlId
     * @param  string  $smsText
     */
    public static function sendVerify(string $mobile, int $otp)
    {
        try {

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.sms.ir/v1/send/verify',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                    "mobile": "'.$mobile.'",
                    "templateId": 110134,
                    "parameters": [
                        {
                            "name": "OTP",
                            "value": "'.$otp.'"
                        }
                    ]
                }',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: text/plain',
                    'x-api-key: fJudg1G1reAMigTBKAvYiuIo2gd7zJcbsnDWiBvbRVoPmdEI', // sandbox
                    // 'x-api-key: qV6dH8VbPg9dggmf4bZhzmKskfey4D8ByZ6s3XzaY3eLhz53'//production
                ],
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response);
            // dd($response->status);

            if (isset($response->status) && $response->status == true) {
                return [
                    'success' => true,
                    'message' => $response->message,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $response->message,
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
