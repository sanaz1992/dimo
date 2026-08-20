<?php

namespace Modules\Instagram\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Http\Controllers\CoreController;

class InstagramWebhookController extends CoreController
{
    /**
     * Verify Instagram webhook.
     */
    public function verify(Request $request)
    {
        Log::info(
            'Instagram Webhook Verification',
            $request->query()
        );

        $mode = $request->query('hub_mode');

        $token = $request->query('hub_verify_token');

        $challenge = $request->query('hub_challenge');

        if (
            $mode === 'subscribe' &&
            hash_equals(
                (string) config(
                    'instagram.meta.webhook_verify_token'
                ),
                (string) $token
            )
        ) {
            return response(
                $challenge,
                200
            )->header(
                'Content-Type',
                'text/plain'
            );
        }

        Log::warning(
            'Instagram Webhook Verification Failed',
            [
                'mode' => $mode,
            ]
        );

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

        Log::info('=== INSTAGRAM WEBHOOK ===', [
            'object' => $payload['object'] ?? null,
            'payload' => $payload,
        ]);

        foreach ($payload['entry'] ?? [] as $entry) {

            foreach ($entry['messaging'] ?? [] as $event) {

                Log::info('=== INSTAGRAM MESSAGING EVENT ===', [
                    'account_id' => $entry['id'] ?? null,

                    'sender_id' => $event['sender']['id'] ?? null,

                    'recipient_id' => $event['recipient']['id'] ?? null,

                    'message' => $event['message'] ?? null,

                    'read' => $event['read'] ?? null,

                    'has_message' => isset($event['message']),

                    'has_read' => isset($event['read']),

                    'raw' => $event,
                ]);
            }

            foreach ($entry['changes'] ?? [] as $change) {

                Log::info('=== INSTAGRAM CHANGE EVENT ===', [
                    'account_id' => $entry['id'] ?? null,
                    'field' => $change['field'] ?? null,
                    'value' => $change['value'] ?? null,
                ]);
            }
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
