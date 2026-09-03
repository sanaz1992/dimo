<?php

namespace Modules\Instagram\Services\Webhook;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstagramComment;
use Modules\Instagram\Services\InstagramCommentService;
use Modules\Instagram\Services\InstagramPostService;

class InstagramCommentWebhookHandler
{
    public function __construct(
        private InstagramPostService $postService,
        private InstagramCommentService $commentService,
    ) {}

    public function handle(array $change, $instagramAccount, ?int $timestamp = null): ?InstagramComment
    {
        $value = $change['value'] ?? [];

        $commentId = $value['id'] ?? null;
        $commenterId = $value['from']['id'] ?? null;
        $commenterUsername = $value['from']['username'] ?? null;
        $text = $value['text'] ?? null;
        $mediaId = $value['media']['id'] ?? null;
        $mediaProductType = $value['media']['media_product_type'] ?? null;

        if (! $commentId || ! $commenterId || ! $mediaId) {
            Log::warning('Instagram comment data incomplete', [
                'webhook_comment_id' => $commentId,
            ]);

            return null;
        }

        $post = $this->postService->firstOrCreate(
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

        return $this->commentService->firstOrCreate(
            [
                'instagram_account_id' => $instagramAccount->id,
                'instagram_comment_id' => $commentId,
            ],
            [
                'instagram_post_id' => $post->id,
                'commenter_ig_id' => $commenterId,
                'commenter_username' => $commenterUsername,
                'comment_text' => $text,
                'commented_at' => $timestamp ? Carbon::createFromTimestamp($timestamp) : now(),
                'payload' => $value,
            ]
        );
    }
}
