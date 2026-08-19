<?php

namespace Modules\Instagram\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Instagram\Services\MetaAuthService;

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
        $tenantId = $request->user()->tenant_id;

        if (! $tenantId) {
            return back()->with(
                'error',
                'هیچ فضای کاری (Tenant) فعالی انتخاب نشده است.'
            );
        }

        return redirect()->away(
            $this->metaService->getAuthorizationUrl($tenantId)
        );
    }

    /**
     * Instagram OAuth callback.
     */
    public function callback(Request $request)
    {
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
             * 1. Validate state
             */
            $stateData = $this->metaService
                ->validateState($state);

            $tenantId = $stateData['tenant_id'];

            /*
             * 2. Exchange code for short-lived token
             */
            $shortTokenData = $this->metaService
                ->exchangeCodeForAccessToken($code);

            if (
                empty($shortTokenData['access_token'])
            ) {
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

            $accessToken =
                $longTokenData['access_token']
                ?? $shortToken;

            /*
             * 4. Get Instagram account
             */
            $instagram = $this->metaService
                ->getInstagramAccount($accessToken);

            /*
             * 5. Save account
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

            return redirect('/admin/dashboard')->with(
                'success',
                'اکانت Instagram با موفقیت متصل شد.'
            );

        } catch (\Throwable $e) {

            report($e);

            return redirect('/admin/dashboard')->with(
                'error',
                'خطا در اتصال Instagram: '.
                $e->getMessage()
            );
        }
    }
}
