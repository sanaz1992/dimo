<?php

namespace Modules\Instagram\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstagramComment;
use Modules\Instagram\Entities\InstagramPost;
use Modules\Instagram\Entities\Message;
use Modules\Instagram\Enums\ConversationStatus;
use Modules\Instagram\Enums\MessageDirection;
use Modules\Instagram\Enums\MessageType;
use Modules\Instagram\Enums\WebhookEventStatus;
use Modules\Instagram\Enums\WebhookEventType;
use Modules\Instagram\Services\ConversationService;
use Modules\Instagram\Services\InstagramAccountService;
use Modules\Instagram\Services\MessageService;
use Modules\Instagram\Services\MetaAuthService;
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

            Log::info('=== INSTAGRAM EVENT PAYLOAD ===', [
                'payload' => $payload,
            ]);

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

                /*
                |--------------------------------------------------------------------------
                | Change Events
                |--------------------------------------------------------------------------
                */

                foreach ($entry['changes'] ?? [] as $change) {

                    $field = $change['field'] ?? null;

                    Log::info('=== INSTAGRAM CHANGE EVENT ===', [
                        'webhook_event_id' => $webhookEvent->id,
                        'entry_id' => $entry['id'] ?? null,
                        'field' => $field,
                        'change' => $change,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Comment
                    |--------------------------------------------------------------------------
                    */

                    if ($field !== 'comments') {
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

                    $instagramAccount = app(
                        InstagramAccountService::class
                    )->findByColumn(
                        'instagram_user_id',
                        $instagramUserId
                    );

                    if (! $instagramAccount) {
                        Log::warning('Instagram account not found for comment', [
                            'instagram_user_id' => $instagramUserId,
                            'webhook_event_id' => $webhookEvent->id,
                        ]);

                        continue;
                    }

                    Log::info('=== INSTAGRAM COMMENT ACCOUNT RESOLVED ===', [
                        'instagram_account_id' => $instagramAccount->id,
                        'tenant_id' => $instagramAccount->tenant_id,
                        'instagram_user_id' => $instagramAccount->instagram_user_id,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Handle Comment
                    |--------------------------------------------------------------------------
                    */

                    $this->handleComment(
                        $change,
                        $instagramAccount,
                        $entry['time'] ?? null
                    );
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

        $metaAuthService = app(MetaAuthService::class);

        $customerProfile = $metaAuthService->getProfile(
            $instagramAccount,
            $senderId
        );

        $conversation = app(ConversationService::class)->firstOrCreate(
            [
                'tenant_id' => $instagramAccount->tenant_id,
                'instagram_account_id' => $instagramAccount->id,
                'customer_ig_id' => $senderId,
            ],
            [
                'customer_username' => $customerProfile['username'] ?? null,
                'status' => ConversationStatus::OPEN->value,
                'last_message_at' => now(),
                'customer_profile_picture_url' => $customerProfile['profile_picture_url'] ?? null,
            ]
        );
        if (! $conversation->customer_username) {
            $conversation->customer_username = $customerProfile['username'] ?? null;
            $conversation->customer_profile_picture_url = $customerProfile['profile_picture_url'] ?? null;
            $conversation->save();
        }

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

    private function handleComment(
        array $change,
        $instagramAccount,
        ?int $timestamp = null
    ): void {
        $value = $change['value'] ?? [];
        $commentId = $value['id'] ?? null;
        $commenterId = $value['from']['id'] ?? null;
        $commenterUsername = $value['from']['username'] ?? null;
        $text = $value['text'] ?? null;
        $mediaId = $value['media']['id'] ?? null;
        $mediaProductType = $value['media']['media_product_type'] ?? null;

        if (! $commentId || ! $commenterId || ! $mediaId) {
            Log::warning(
                'Instagram comment data incomplete',
                [
                    'webhook_event_id' => $this->webhookEventId,
                    'change' => $change,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Create / Find Post
        |--------------------------------------------------------------------------
        */

        $post = InstagramPost::firstOrCreate(
            [
                'instagram_account_id' => $instagramAccount->id,

                'instagram_media_id' => $mediaId,
            ],
            [
                'media_product_type' => $mediaProductType,

                'caption' => null,

                'permalink' => null,

                'published_at' => null,

                'payload' => $value['media'] ?? [],
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | Create / Find Comment
    |--------------------------------------------------------------------------
    */

        $comment = InstagramComment::firstOrCreate(
            [
                'instagram_account_id' => $instagramAccount->id,

                'instagram_comment_id' => $commentId,
            ],
            [
                'instagram_post_id' => $post->id,

                'commenter_ig_id' => $commenterId,

                'commenter_username' => $commenterUsername,

                'comment_text' => $text,

                'commented_at' => $timestamp
                    ? Carbon::createFromTimestamp($timestamp)
                    : now(),

                'payload' => $value,
            ]
        );

        Log::info(
            '=== INSTAGRAM COMMENT CREATED/FOUND ===',
            [
                'comment_id' => $comment->id,

                'instagram_comment_id' => $comment->instagram_comment_id,

                'post_id' => $post->id,

                'media_id' => $post->instagram_media_id,

                'commenter_ig_id' => $comment->commenter_ig_id,

                'commenter_username' => $comment->commenter_username,

                'text' => $comment->comment_text,
            ]
        );
    }
}
