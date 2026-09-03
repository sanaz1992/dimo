<?php

namespace Modules\Instagram\Services\Webhook;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Enums\ConversationStatus;
use Modules\Instagram\Enums\MessageDirection;
use Modules\Instagram\Enums\MessageType;
use Modules\Instagram\Services\ConversationService;
use Modules\Instagram\Services\MessageService;
use Modules\Instagram\Services\MetaAuthService;

class InstagramMessageWebhookHandler
{
    public function __construct(
        private MetaAuthService $metaAuthService,
        private ConversationService $conversationService,
        private MessageService $messageService,
    ) {}

    public function handle(array $event, $instagramAccount): void
    {
        $message = $event['message'] ?? [];

        $senderId = $event['sender']['id'] ?? null;
        $recipientId = $event['recipient']['id'] ?? null;
        $instagramMessageId = $message['mid'] ?? null;
        $text = $message['text'] ?? null;

        if (! $senderId || ! $recipientId || ! $instagramMessageId) {
            Log::warning('Instagram message data incomplete', [
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'instagram_message_id' => $instagramMessageId,
            ]);

            return;
        }

        $customerProfile = $this->metaAuthService->getProfile(
            $instagramAccount,
            $senderId
        );

        $conversation = $this->conversationService->firstOrCreate(
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
            $conversation->update([
                'customer_username' => $customerProfile['username'] ?? null,
                'customer_profile_picture_url' => $customerProfile['profile_picture_url'] ?? null,
            ]);
        }

        $messageModel = $this->messageService->firstOrCreate(
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
                'sent_at' => isset($event['timestamp']) ? Carbon::createFromTimestampMs($event['timestamp']) : null,
            ]
        );

        $this->conversationService->update(
            $conversation,
            ['last_message_at' => $messageModel->sent_at ?? now()]
        );
    }
}
