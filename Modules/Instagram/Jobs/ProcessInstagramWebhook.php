<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Enums\WebhookEventStatus;
use Modules\Instagram\Enums\WebhookEventType;
use Modules\Instagram\Services\InstagramAccountService;
use Modules\Instagram\Services\WebhookEventService;

class ProcessInstagramWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $webhookEventId,
        protected WebhookEventService $webhookService
    ) {}

    public function handle(): void
    {
        $webhookEvent = $this->webhookService->findOrFail($this->webhookEventId);

        $webhookEvent = $this->webhookService->update($webhookEvent, [
            'status' => WebhookEventStatus::PROCESSING->value,
        ]);

        try {
            $payload = $webhookEvent->payload;
            foreach ($payload['entry'] ?? [] as $entry) {

                $instagramUserId = $entry['id'] ?? null;
                $instagramAccount = app(InstagramAccountService::class)->findByColumn('instagram_user_id', $instagramUserId);

                if (! $instagramAccount) {
                    Log::warning(
                        'Instagram account not found',
                        [
                            'instagram_user_id' => $instagramUserId,
                            'webhook_event_id' => $webhookEvent->id,
                        ]
                    );

                    continue;
                }

                foreach ($entry['messaging'] ?? [] as $event) {
                    $eventType = $this->detectEventType($event);
                    Log::info(
                        '=== INSTAGRAM EVENT DETECTED ===',
                        [
                            'webhook_event_id' => $webhookEvent->id,
                            'instagram_account_id' => $instagramAccount->id,
                            'tenant_id' => $instagramAccount->tenant_id,
                            'instagram_user_id' => $instagramAccount->instagram_user_id,
                            'event_type' => $eventType,
                            'event' => $event,
                        ]
                    );

                    // فعلاً فقط تشخیص می‌دهیم.
                    // مرحله بعد processing واقعی را اضافه می‌کنیم.
                }
            }

            $webhookEvent = $this->webhookService->update($webhookEvent, [
                'status' => WebhookEventStatus::PROCESSED->value,
                'processed_at' => now(),
            ]);

        } catch (\Throwable $e) {
            $webhookEvent = $this->webhookService->update($webhookEvent, [
                'status' => WebhookEventStatus::FAILED->value,
                'error' => $e->getMessage(),
            ]);
            throw $e;
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
