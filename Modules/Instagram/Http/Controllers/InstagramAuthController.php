<?php

namespace Modules\Instagram\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Instagram\Services\MetaAuthService;
use Modules\Tenant\Enums\TenantStatus;
use Modules\Tenant\Services\TenantService;

class InstagramAuthController extends CoreController
{
    public function __construct(
        protected MetaAuthService $metaService
    ) {}

    /**
     * Start Instagram Business Login.
     */
    public function redirect(Request $request)
    {
        $tenantSlug = $request->get('tenant');
        if (! $tenantSlug) {
            if (request()->routeIs('admin.*')) {
                $routeName = 'admin.tenants.index';
            } else {
                $routeName = 'user.tenants.index';
            }

            return redirect()->route($routeName)->with(
                'error',
                'هیچ  کسب و کار فعالی انتخاب نشده است.لطفا از لیست کسب و کارها دکمه اتصال برای کسب و کار مورد نظر را انتخاب کنید.'
            );
        }

        $tenant = app(TenantService::class)->findByColumn('slug', $tenantSlug);
        if (! $tenant || $tenant->status != TenantStatus::ACTIVE) {
            if (request()->routeIs('admin.*')) {
                $routeName = 'admin.tenants.index';
            } else {
                $routeName = 'user.tenants.index';
            }

            return redirect()->route($routeName)->with(
                'error',
                'هیچ  کسب و کار فعالی انتخاب نشده است.لطفا از لیست کسب و کارها دکمه اتصال برای کسب و کار مورد نظر را انتخاب کنید.'
            );
        }

        return redirect()->away(
            $this->metaService->getAuthorizationUrl($tenant->id)
        );
    }

    /**
     * Instagram OAuth callback.
     */
    public function callback(Request $request)
    {
        /*
         * User cancelled OAuth.
         */
        if ($request->filled('error')) {
            return redirect('/admin/dashboard')->with(
                'error',
                'اتصال Instagram توسط کاربر لغو شد.'
            );
        }

        $code = $request->input('code');
        $state = $request->input('state');

        if (! $code || ! $state) {
            return redirect('/admin/dashboard')->with(
                'error',
                'پاسخ نامعتبر از Instagram دریافت شد.'
            );
        }

        try {

            /*
             * 1. Validate OAuth state
             */
            $stateData = $this->metaService->validateState($state);

            $tenantId = $stateData['tenant_id'];

            /*
             * 2. Exchange authorization code
             *    for short-lived access token
             */
            $shortTokenData = $this->metaService
                ->exchangeCodeForAccessToken($code);

            if (empty($shortTokenData['access_token'])) {
                throw new \Exception(
                    'Instagram Access Token در پاسخ Meta وجود ندارد.'
                );
            }

            $shortToken = $shortTokenData['access_token'];

            /*
             * 3. Exchange short-lived token
             *    for long-lived token
             */
            $longTokenData = $this->metaService
                ->exchangeForLongLivedToken($shortToken);

            $accessToken = $longTokenData['access_token']
                ?? $shortToken;

            Log::info('INSTAGRAM TOKEN DEBUG', [
                'token_length' => strlen($accessToken),
                'token_start' => substr($accessToken, 0, 20),
                'token_end' => substr($accessToken, -20),
                'long_token_response' => $longTokenData,
            ]);
            /*
             * 4. Get Instagram account information
             */
            $instagram = $this->metaService
                ->getInstagramAccount($accessToken);

            if (empty($instagram['id'])) {
                throw new \Exception(
                    'Instagram User ID از Meta دریافت نشد.'
                );
            }

            /*
             * 5. Subscribe this Instagram account
             *    to webhook events.
             *
             *    This is the automatic equivalent of:
             *
             *    POST /{instagram-user-id}/subscribed_apps
             */
            $subscribed = $this->metaService->subscribedApps(
                $instagram['id'],
                $accessToken
            );

            if (empty($subscribed['success'])) {
                throw new \Exception(
                    'فعال‌سازی Instagram Webhook موفق نبود.'
                );
            }

            /*
             * 6. Save Instagram account
             *
             *    We only save the account as connected
             *    after webhook subscription succeeds.
             */
            InstagramAccount::updateOrCreate(
                [
                    'instagram_account_id' => $instagram['id'],
                ],
                [
                    'tenant_id' => $tenantId,

                    'username' => $instagram['username'] ?? null,

                    'name' => $instagram['name'] ?? null,

                    'profile_picture_url' => $instagram['profile_picture_url'] ?? null,

                    'access_token' => $accessToken,

                    'token_expires_at' => isset($longTokenData['expires_in'])
                        ? now()->addSeconds(
                            $longTokenData['expires_in']
                        )
                        : now()->addDays(60),

                    'scopes' => $shortTokenData['permissions']
                        ?? $this->metaService->getScopes(),

                    'status' => 'connected',

                    'connected_at' => Carbon::now(),

                    'last_synced_at' => Carbon::now(),
                ]
            );

            /*
             * 7. Redirect user back to dashboard
             */
            return redirect('/admin/dashboard')->with(
                'success',
                'اکانت Instagram با موفقیت متصل و Webhook آن فعال شد.'
            );
        } catch (\Throwable $e) {

            report($e);

            return redirect('/admin/dashboard')->with(
                'error',
                'خطا در اتصال Instagram: '.$e->getMessage()
            );
        }
    }
}
