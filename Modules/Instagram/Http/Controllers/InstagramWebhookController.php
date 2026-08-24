<?php

namespace Modules\Instagram\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Instagram\Jobs\ProcessInstagramWebhook;
use Modules\Instagram\Services\WebhookEventService;

class InstagramWebhookController extends CoreController
{
    /**
     * Verify Instagram webhook.
     */
    public function verify(Request $request)
    {
        Log::info('Instagram Webhook Verification', $request->query());

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && hash_equals((string) config('instagram.meta.webhook_verify_token'), (string) $token)) {
            return response(
                $challenge,
                200
            )->header('Content-Type', 'text/plain');
        }

        Log::warning('Instagram Webhook Verification Failed', ['mode' => $mode]);

        return response(
            'Forbidden',
            403
        );
    }

    /**
     * Receive Instagram webhook events.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        $webhookEvent = app(WebhookEventService::class)->create([
            'provider' => 'instagram',
            'payload' => $payload,
            'status' => 'pending',
        ]);

        ProcessInstagramWebhook::dispatch(
            $webhookEvent->id
        );

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
