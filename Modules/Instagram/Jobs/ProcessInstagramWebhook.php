<?php

namespace Modules\Instagram\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\Message;
use Modules\Instagram\Enums\ConversationStatus;
use Modules\Instagram\Enums\MessageDirection;
use Modules\Instagram\Enums\MessageType;
use Modules\Instagram\Enums\WebhookEventStatus;
use Modules\Instagram\Enums\WebhookEventType;
use Modules\Instagram\Services\ConversationService;
use Modules\Instagram\Services\InstagramAccountService;
use Modules\Instagram\Services\MessageService;
use Modules\Instagram\Services\WebhookEventService;

class ProcessInstagramWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $webhookEventId
    ) {}

    public function handle(
        WebhookEventService $webhookService
    ): void {

        $webhookEvent = $webhookService->findOrFail(
            $this->webhookEventId
        );

        $webhookEvent = $webhookService->update($webhookEvent, [
            'status' => WebhookEventStatus::PROCESSING->value,
        ]);

        try {
            $payload = $webhookEvent->payload;

            foreach ($payload['entry'] ?? [] as $entry) {

                foreach ($entry['messaging'] ?? [] as $event) {

                    $eventType = $this->detectEventType($event);

                    Log::info('=== INSTAGRAM EVENT DETECTED ===', [
                        'webhook_event_id' => $webhookEvent->id,
                        'entry_id' => $entry['id'] ?? null,
                        'event_type' => $eventType,
                        'event' => $event,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Ignore events that we don't want to process
                    |--------------------------------------------------------------------------
                    */

                    if ($eventType === WebhookEventType::MESSAGE_EDIT->value || $eventType === WebhookEventType::MESSAGE_ECHO->value) {
                        Log::info('=== INSTAGRAM EVENT IGNORED ===', [
                            'webhook_event_id' => $webhookEvent->id,
                            'event_type' => $eventType,
                        ]);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Resolve Instagram Account
                    |--------------------------------------------------------------------------
                    */

                    $instagramUserId = $entry['id'] ?? null;
                    if (! $instagramUserId) {
                        Log::warning('Instagram entry ID missing', [
                            'webhook_event_id' => $webhookEvent->id,
                        ]);

                        continue;
                    }

                    $instagramAccount = app(InstagramAccountService::class)->findByColumn('instagram_user_id', $instagramUserId);
                    if (! $instagramAccount) {
                        Log::warning('Instagram account not found', [
                            'instagram_user_id' => $instagramUserId,
                            'webhook_event_id' => $webhookEvent->id,
                            'event_type' => $eventType,
                        ]);

                        continue;
                    }

                    Log::info('=== INSTAGRAM ACCOUNT RESOLVED ===', [
                        'instagram_account_id' => $instagramAccount->id,
                        'tenant_id' => $instagramAccount->tenant_id,
                        'instagram_user_id' => $instagramAccount->instagram_user_id,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Process Message
                    |--------------------------------------------------------------------------
                    */

                    if ($eventType === WebhookEventType::MESSAGE->value) {
                        $result = $this->handleMessage($event, $instagramAccount);
                        if ($result == 'continue') {
                            continue;
                        }
                    }
                }
            }

            $webhookService->update($webhookEvent, [
                'status' => WebhookEventStatus::PROCESSED->value,
                'processed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $webhookService->update($webhookEvent, [
                'status' => WebhookEventStatus::FAILED->value,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function handleMessage(array $event, $instagramAccount)
    {
        $message = $event['message'] ?? [];
        $senderId = $event['sender']['id'] ?? null;
        $recipientId = $event['recipient']['id'] ?? null;
        $instagramMessageId = $message['mid'] ?? null;
        $text = $message['text'] ?? null;

        Log::info('=== INSTAGRAM MESSAGE DATA ===', [
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'instagram_message_id' => $instagramMessageId,
            'text' => $text,
        ]);

        if (! $senderId || ! $recipientId || ! $instagramMessageId) {
            Log::warning('Instagram message data incomplete', [
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'instagram_message_id' => $instagramMessageId,
                'event' => $event,
            ]);

            return 'continue';
        }

        $conversation = app(ConversationService::class)->firstOrCreate(
            [
                'tenant_id' => $instagramAccount->tenant_id,
                'instagram_account_id' => $instagramAccount->id,
                'customer_ig_id' => $senderId,
            ],
            [
                'customer_username' => null,
                'status' => ConversationStatus::OPEN->value,
                'last_message_at' => now(),
            ]
        );

        Log::info('=== CONVERSATION CREATED/FOUND ===', [
            'conversation_id' => $conversation->id,
        ]);

        $messageModel = app(MessageService::class)->firstOrCreate(
            [
                'conversation_id' => $conversation->id,
                'instagram_message_id' => $instagramMessageId,
            ],
            [
                'sender_ig_id' => $senderId,
                'recipient_ig_id' => $recipientId,
                'direction' => MessageDirection::INCOMING->value,
                'type' => MessageType::TEXT->value,
                'message_body' => $text,
                'payload' => $event,
                'sent_at' => isset($event['timestamp'])
                    ? Carbon::createFromTimestampMs($event['timestamp'])
                    : null,
            ]
        );

        Log::info('=== MESSAGE CREATED/FOUND ===', [
            'message_id' => $messageModel->id,
        ]);

        app(ConversationService::class)->update($conversation, [
            'last_message_at' => $messageModel->sent_at ?? now(),
        ]);

        return true;
    }

    private function detectEventType(array $event): string
    {
        if (isset($event['message_edit'])) {
            return WebhookEventType::MESSAGE_EDIT->value;
        }

        if (isset($event['message'])) {

            if (
                ($event['message']['is_echo'] ?? false) === true
            ) {
                return WebhookEventType::MESSAGE_ECHO->value;
            }

            return WebhookEventType::MESSAGE->value;
        }

        if (isset($event['read'])) {
            return WebhookEventType::READ->value;
        }

        if (isset($event['postback'])) {
            return WebhookEventType::POSTBACK->value;
        }

        return WebhookEventType::UNKNOWN->value;
    }
}
