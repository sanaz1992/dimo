<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\WebhookEvent;
use Modules\Instagram\Enums\WebhookEventType;
use Modules\Instagram\Jobs\ProcessInstagramCommentAutomation;
use Modules\Instagram\Services\Webhook\InstagramCommentWebhookHandler;
use Modules\Instagram\Services\Webhook\InstagramMessageWebhookHandler;

class InstagramWebhookService
{
    public function __construct(
        private InstagramAccountService $instagramAccountService,
        private InstagramMessageWebhookHandler $messageHandler,
        private InstagramCommentWebhookHandler $commentHandler,
    ) {}

    public function process(WebhookEvent $webhookEvent): void
    {
        $payload = $webhookEvent->payload;
        foreach ($payload['entry'] ?? [] as $entry) {
            $this->processEntry(
                $entry,
                $webhookEvent->id
            );
        }
    }

    private function processEntry(array $entry, int $webhookEventId): void
    {
        $instagramUserId = $entry['id'] ?? null;
        if (! $instagramUserId) {
            Log::warning('Instagram entry ID missing', [
                'webhook_event_id' => $webhookEventId,
            ]);

            return;
        }

        $instagramAccount = $this->instagramAccountService->findByColumn(
            'instagram_user_id',
            $instagramUserId
        );
        if (! $instagramAccount) {
            Log::debug('Ignoring webhook for unknown Instagram account', [
                'instagram_user_id' => $instagramUserId,
                'webhook_event_id' => $webhookEventId,
            ]);

            return;
        }

        $this->processMessagingEvents($entry, $instagramAccount);

        $this->processChangeEvents($entry, $instagramAccount);
    }

    private function processMessagingEvents(array $entry, $instagramAccount): void
    {
        foreach ($entry['messaging'] ?? [] as $event) {
            $eventType = $this->detectEventType($event);

            if (
                $eventType === WebhookEventType::MESSAGE_EDIT->value ||
                $eventType === WebhookEventType::MESSAGE_ECHO->value
            ) {
                continue;
            }

            if ($eventType !== WebhookEventType::MESSAGE->value) {
                continue;
            }

            $this->messageHandler->handle($event, $instagramAccount);
        }
    }

    private function processChangeEvents(array $entry, $instagramAccount): void
    {
        foreach ($entry['changes'] ?? [] as $change) {
            $field = $change['field'] ?? null;
            if ($field !== 'comments') {
                continue;
            }

            $comment = $this->commentHandler->handle(
                $change,
                $instagramAccount,
                $entry['time'] ?? null
            );
            if (! $comment || ! $comment->wasRecentlyCreated) {
                continue;
            }

            ProcessInstagramCommentAutomation::dispatch($comment->id)->onQueue('instagram-automation');
        }
    }

    private function detectEventType(array $event): string
    {
        if (isset($event['message_edit'])) {
            return WebhookEventType::MESSAGE_EDIT->value;
        }

        if (isset($event['message'])) {
            if (($event['message']['is_echo'] ?? false) === true) {
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
