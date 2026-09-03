<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Enums\WebhookEventStatus;
use Modules\Instagram\Services\InstagramWebhookService;
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
        WebhookEventService $webhookEventService,
        InstagramWebhookService $instagramWebhookService
    ): void {
        $webhookEvent = $webhookEventService->findOrFail(
            $this->webhookEventId
        );

        $webhookEventService->update($webhookEvent, [
            'status' => WebhookEventStatus::PROCESSING->value,
        ]);

        try {
            $instagramWebhookService->process($webhookEvent);

            $webhookEventService->update($webhookEvent, [
                'status' => WebhookEventStatus::PROCESSED->value,
                'processed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $webhookEventService->update($webhookEvent, [
                'status' => WebhookEventStatus::FAILED->value,
                'error' => $e->getMessage(),
            ]);

            Log::error('Instagram webhook processing failed', [
                'webhook_event_id' => $webhookEvent->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
