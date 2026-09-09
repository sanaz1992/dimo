<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Instagram\Entities\Message;
use Modules\Instagram\Enums\ConversationStatus;
use Modules\Instagram\Enums\MessageDirection;
use Modules\Instagram\Enums\MessageSource;
use Modules\Instagram\Enums\MessageType;

class InstagramMessageService
{
    public function __construct(
        protected ConversationService $conversationService,
        protected MessageService $messageService,
    ) {}

    public function sendTextMessage(
        InstagramAccount $instagramAccount,
        string $recipientIgId,
        string $message,
        string $source = MessageSource::AUTOMATION->value
    ): array {
        $response = Http::withToken($instagramAccount->access_token)->post(
            $this->getMessagesEndpoint($instagramAccount),
            [
                'recipient' => ['id' => $recipientIgId],
                'message' => ['text' => $message],
            ]
        );

        if ($response->failed()) {
            throw new \RuntimeException('Instagram message sending failed: '.$response->body());
        }

        $result = $response->json();

        $this->storeOutgoingMessage(
            instagramAccount: $instagramAccount,
            recipientIgId: $recipientIgId,
            recipientUsername: null,
            message: $message,
            result: $result,
            source: $source
        );

        Log::info(
            'Instagram message sent.',
            [
                'instagram_account_id' => $instagramAccount->id,
                'recipient_id' => $recipientIgId,
                'message_id' => $result['message_id'] ?? null,
            ]
        );

        return $result;
    }

    public function sendPrivateReply(
        InstagramAccount $instagramAccount,
        string $commentId,
        string $recipientIgId,
        ?string $recipientUsername,
        string $message,
        string $source = MessageSource::AUTOMATION->value
    ): array {
        $response = Http::withToken($instagramAccount->access_token)->post(
            $this->getMessagesEndpoint($instagramAccount),
            [
                'recipient' => ['comment_id' => $commentId],
                'message' => ['text' => $message],
            ]
        );

        if ($response->failed()) {
            Log::error(
                'Instagram private reply failed.',
                [
                    'instagram_account_id' => $instagramAccount->id,
                    'comment_id' => $commentId,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]
            );

            throw new \RuntimeException('Instagram private reply failed: '.$response->body());
        }

        $result = $response->json();

        $this->storeOutgoingMessage(
            instagramAccount: $instagramAccount,
            recipientIgId: $recipientIgId,
            recipientUsername: $recipientUsername,
            message: $message,
            result: $result,
            source: $source
        );

        Log::info(
            'Instagram private reply sent.',
            [
                'instagram_account_id' => $instagramAccount->id,
                'comment_id' => $commentId,
                'recipient_id' => $recipientIgId,
                'message_id' => $result['message_id'] ?? null,
            ]
        );

        return $result;
    }

    private function storeOutgoingMessage(
        InstagramAccount $instagramAccount,
        string $recipientIgId,
        ?string $recipientUsername,
        string $message,
        array $result,
        string $source
    ): Message {
        $conversation = $this->findOrCreateConversation(
            instagramAccount: $instagramAccount,
            recipientIgId: $recipientIgId,
            recipientUsername: $recipientUsername,
        );

        $messageModel = $this->messageService->create([
            'conversation_id' => $conversation->id,
            'instagram_message_id' => $result['message_id'] ?? null,
            'sender_ig_id' => $instagramAccount->instagram_user_id,
            'recipient_ig_id' => $recipientIgId,
            'direction' => MessageDirection::OUTGOING->value,
            'type' => MessageType::TEXT->value,
            'message_body' => $message,
            'payload' => $result,
            'sent_at' => now(),
            'source' => $source,
        ]);

        $this->conversationService->update($conversation, ['last_message_at' => $messageModel->sent_at]);

        return $messageModel;
    }

    private function findOrCreateConversation(
        InstagramAccount $instagramAccount,
        string $recipientIgId,
        ?string $recipientUsername
    ) {
        $conversation = $this->conversationService->firstOrCreate(
            [
                'tenant_id' => $instagramAccount->tenant_id,
                'instagram_account_id' => $instagramAccount->id,
                'customer_ig_id' => $recipientIgId,
            ],
            [
                'customer_username' => $recipientUsername,
                'status' => ConversationStatus::OPEN->value,
                'last_message_at' => now(),
            ]
        );

        if ($recipientUsername && $conversation->customer_username !== $recipientUsername) {
            $this->conversationService->update($conversation, ['customer_username' => $recipientUsername]);
        }

        return $conversation;
    }

    private function getMessagesEndpoint(InstagramAccount $instagramAccount): string
    {
        return 'https://graph.instagram.com/v26.0/'.$instagramAccount->instagram_user_id.'/messages';
    }
}
