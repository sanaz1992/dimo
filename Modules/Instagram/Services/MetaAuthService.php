<?php

namespace Modules\Instagram\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Instagram\Entities\InstagramAccount;

class MetaAuthService
{
    protected string $appId;

    protected string $appSecret;

    protected string $redirectUri;

    protected string $graphVersion = 'v26.0';

    public function __construct()
    {
        $this->appId = config('instagram.meta.app_id');

        $this->appSecret = config('instagram.meta.app_secret');

        $this->redirectUri = config('instagram.meta.redirect_uri');
    }

    /**
     * Instagram Business Login permissions.
     */
    public function getScopes(): array
    {
        return [
            'instagram_business_basic',
            'instagram_business_manage_messages',
            'instagram_business_manage_comments',
        ];
    }

    /**
     * Generate OAuth state.
     */
    public function createState(int $tenantId): string
    {
        return encrypt([
            'tenant_id' => $tenantId,
            'nonce' => Str::random(40),
            'created_at' => now()->timestamp,
        ]);
    }

    /**
     * Validate OAuth state.
     */
    public function validateState(string $state): array
    {
        try {
            $data = decrypt($state);
        } catch (\Throwable $e) {
            throw new Exception(
                'Instagram OAuth state نامعتبر است.'
            );
        }

        if (
            ! isset($data['tenant_id']) ||
            ! isset($data['nonce']) ||
            ! isset($data['created_at'])
        ) {
            throw new Exception(
                'اطلاعات OAuth state ناقص است.'
            );
        }

        /*
         * State expiration: 10 minutes.
         */
        if (
            now()->timestamp - $data['created_at'] > 600
        ) {
            throw new Exception(
                'Instagram OAuth session منقضی شده است.'
            );
        }

        return $data;
    }

    /**
     * Generate Instagram Business Login URL.
     */
    public function getAuthorizationUrl(int $tenantId): string
    {
        $state = $this->createState($tenantId);

        $params = [
            'force_reauth' => 'true',

            'client_id' => $this->appId,

            'redirect_uri' => $this->redirectUri,

            'response_type' => 'code',

            'scope' => implode(
                ',',
                $this->getScopes()
            ),

            'state' => $state,
        ];

        $url =
            'https://www.instagram.com/oauth/authorize?'.
            http_build_query($params);

        Log::info('Instagram OAuth URL', [
            'app_id' => $this->appId,

            'redirect_uri' => $this->redirectUri,

            /*
             * Do not log access tokens or secrets.
             */
            'scopes' => $this->getScopes(),
        ]);

        return $url;
    }

    /**
     * Exchange authorization code
     * for short-lived Instagram token.
     */
    public function exchangeCodeForAccessToken(
        string $code
    ): array {
        $response = Http::asMultipart()->post(
            'https://api.instagram.com/oauth/access_token',
            [
                [
                    'name' => 'client_id',
                    'contents' => $this->appId,
                ],
                [
                    'name' => 'client_secret',
                    'contents' => $this->appSecret,
                ],
                [
                    'name' => 'grant_type',
                    'contents' => 'authorization_code',
                ],
                [
                    'name' => 'redirect_uri',
                    'contents' => $this->redirectUri,
                ],
                [
                    'name' => 'code',
                    'contents' => $code,
                ],
            ]
        );

        Log::info('Instagram Token Exchange', [
            'app_id' => $this->appId,

            'redirect_uri' => $this->redirectUri,

            'code_length' => strlen($code),
        ]);

        if ($response->failed()) {
            throw new Exception(
                'خطا در دریافت Instagram Access Token: '.
                    $response->body()
            );
        }

        return $response->json();
    }

    /**
     * Exchange short-lived token
     * for long-lived token.
     */
    public function exchangeForLongLivedToken(
        string $shortLivedToken
    ): array {
        $response = Http::get(
            'https://graph.instagram.com/access_token',
            [
                'grant_type' => 'ig_exchange_token',

                'client_secret' => $this->appSecret,

                'access_token' => $shortLivedToken,
            ]
        );

        if ($response->failed()) {
            throw new Exception(
                'خطا در دریافت Instagram Long-Lived Token: '.
                    $response->body()
            );
        }

        return $response->json();
    }

    /**
     * Get Instagram account information.
     */
    public function getInstagramAccount(
        string $accessToken
    ): array {
        $response = Http::get(
            'https://graph.instagram.com/me',
            [
                'fields' => 'id,username,name,account_type,profile_picture_url,user_id',

                'access_token' => $accessToken,
            ]
        );

        if ($response->failed()) {
            throw new Exception(
                'خطا در دریافت اطلاعات اکانت Instagram: '.
                    $response->body()
            );
        }

        return $response->json();
    }

    /**
     * Subscribe Instagram account to webhook events.
     *
     * Equivalent to:
     *
     * POST /{instagram-user-id}/subscribed_apps
     *
     * Fields:
     * - messages
     * - comments
     */
    public function subscribedApps(
        string $instagramUserId,
        string $accessToken
    ): array {
        $url =
            "https://graph.instagram.com/{$this->graphVersion}/".
            "{$instagramUserId}/subscribed_apps";

        $response = Http::withToken($accessToken)
            ->post(
                $url,
                [
                    'subscribed_fields' => [
                        'messages',
                        'comments',
                    ],
                ]
            );

        if ($response->failed()) {
            Log::error(
                'Instagram subscribed_apps failed',
                [
                    'instagram_user_id' => $instagramUserId,

                    'status' => $response->status(),

                    'response' => $response->json(),
                ]
            );

            throw new Exception(
                'خطا در فعال‌سازی Instagram Webhook: '.
                    $response->body()
            );
        }

        $data = $response->json();

        Log::info(
            'Instagram subscribed_apps success',
            [
                'instagram_user_id' => $instagramUserId,

                'response' => $data,
            ]
        );

        return $data;
    }

    public function getProfile(
        InstagramAccount $instagramAccount,
        string $instagramUserId
    ): ?array {
        $response = Http::get(
            'https://graph.instagram.com/'.$instagramUserId,
            [
                'fields' => 'id,username,name',
                'access_token' => $instagramAccount->access_token,
            ]
        );

        if ($response->failed()) {
            // Log::warning('Instagram user profile fetch failed', [
            //     'instagram_user_id' => $instagramUserId,
            //     'instagram_account_id' => $instagramAccount->id,
            //     'status' => $response->status(),
            //     'body' => $response->body(),
            // ]);

            return null;
        }

        return $response->json();
    }
}
