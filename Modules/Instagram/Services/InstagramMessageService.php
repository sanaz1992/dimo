<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\Http;
use Modules\Instagram\Entities\Conversation;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Instagram\Entities\Message;
use Modules\Instagram\Enums\ConversationStatus;
use Modules\Instagram\Enums\MessageDirection;
use Modules\Instagram\Enums\MessageType;

class InstagramMessageService
{
    public function sendTextMessage(
        InstagramAccount $instagramAccount,
        string $recipientIgId,
        string $message
    ): array {
        $response = Http::withToken($instagramAccount->access_token)
            ->post(
                'https://graph.instagram.com/v26.0/'.
                    $instagramAccount->instagram_user_id.
                    '/messages',
                [
                    'recipient' => ['id' => $recipientIgId],
                    'message' => ['text' => $message],
                ]
            );

        if ($response->failed()) {
            throw new \RuntimeException('Instagram message sending failed: '.$response->body());
        }

        $result = $response->json();

        /*
         * پیدا کردن یا ساخت Conversation
         */
        $conversation = Conversation::firstOrCreate(
            [
                'tenant_id' => $instagramAccount->tenant_id,
                'instagram_account_id' => $instagramAccount->id,
                'customer_ig_id' => $recipientIgId,
            ],
            [
                'customer_username' => null,
                'status' => ConversationStatus::OPEN->value,
                'last_message_at' => now(),
            ]
        );

        /*
         * ذخیره پیام Outgoing
         */
        $messageModel = Message::create([
            'conversation_id' => $conversation->id,
            'instagram_message_id' => $result['message_id'] ?? null,
            'sender_ig_id' => $instagramAccount->instagram_user_id,
            'recipient_ig_id' => $recipientIgId,
            'direction' => MessageDirection::OUTGOING->value,
            'type' => MessageType::TEXT->value,
            'message_body' => $message,
            'payload' => $result,
            'sent_at' => now(),
        ]);

        /*
         * آپدیت آخرین پیام Conversation
         */
        $conversation->update([
            'last_message_at' => $messageModel->sent_at,
        ]);

        return $result;
    }
}
