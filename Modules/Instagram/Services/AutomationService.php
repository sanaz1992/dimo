<?php

namespace Modules\Instagram\Services;

use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\AutomationAction;
use Modules\Instagram\Entities\AutomationRule;
use Modules\Instagram\Entities\AutomationRun;
use Modules\Instagram\Entities\InstagramComment;
use Modules\Instagram\Enums\AutomationActionType;
use Modules\Instagram\Enums\AutomationMatchType;
use Modules\Instagram\Enums\AutomationRunStatus;

class AutomationService
{
    public function processComment(InstagramComment $comment): void
    {
        $rules = AutomationRule::query()
            ->where('tenant_id', $comment->instagramAccount->tenant_id)
            ->where('instagram_account_id', $comment->instagram_account_id)
            ->where('is_active', true)
            ->where(function ($query) use ($comment) {
                $query->whereNull('instagram_post_id')
                    ->orWhere('instagram_post_id', $comment->instagram_post_id);
            })->orderBy('priority')
            ->get();

        foreach ($rules as $rule) {
            if (! $this->matches($rule, $comment)) {
                continue;
            }

            $alreadyExecuted = AutomationRun::query()
                ->where('automation_rule_id', $rule->id)
                ->where('instagram_comment_id', $comment->id)
                ->exists();
            if ($alreadyExecuted) {
                continue;
            }

            $run = AutomationRun::create([
                'automation_rule_id' => $rule->id,
                'instagram_account_id' => $comment->instagram_account_id,
                'instagram_comment_id' => $comment->id,
                'status' => AutomationRunStatus::PENDING->value,
                'context' => [
                    'comment_text' => $comment->comment_text,
                    'commenter_ig_id' => $comment->commenter_ig_id,
                    'commenter_username' => $comment->commenter_username,
                ],
            ]);

            $this->executeRun($run);
        }
    }

    private function matches(AutomationRule $rule, InstagramComment $comment): bool
    {
        $commentText = trim(mb_strtolower($comment->comment_text ?? ''));
        $matchValue = trim(mb_strtolower($rule->match_value));

        return match ($rule->match_type) {
            AutomationMatchType::EXACT => $commentText === $matchValue,
            AutomationMatchType::CONTAINS => str_contains($commentText, $matchValue),
            AutomationMatchType::STARTS_WITH => str_starts_with($commentText, $matchValue),
            AutomationMatchType::ENDS_WITH => str_ends_with($commentText, $matchValue),
            default => false,
        };
    }

    private function executeRun(AutomationRun $run): void
    {
        $run->update([
            'status' => AutomationRunStatus::PROCESSING->value,
            'started_at' => now(),
        ]);

        try {
            $actions = $run->automationRule
                ->actions()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            foreach ($actions as $action) {
                match ($action->action_type) {
                    AutomationActionType::SEND_MESSAGE => $this->executeSendMessage(
                        $run,
                        $action
                    ),

                    default => Log::warning(
                        'Unsupported automation action',
                        [
                            'run_id' => $run->id,
                            'action_id' => $action->id,
                            'action_type' => $action->action_type,
                        ]
                    ),
                };
            }

            $run->update([
                'status' => AutomationRunStatus::COMPLETED->value,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('=== Instagram automation run failed ===', [
                'run_id' => $run->id,
                'error' => $e->getMessage(),
            ]);

            $run->update([
                'status' => AutomationRunStatus::FAILED->value,
                'error' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    private function executeSendMessage(AutomationRun $run, AutomationAction $action): void
    {
        $comment = $run->instagramComment;
        if (! $comment) {
            throw new \RuntimeException(
                'Instagram comment not found for automation run.'
            );
        }

        $instagramAccount = $run->instagramAccount;
        if (! $instagramAccount) {
            throw new \RuntimeException(
                'Instagram account not found for automation run.'
            );
        }

        $message = trim($action->config['message'] ?? '');
        if ($message === '') {
            throw new \RuntimeException(
                'Automation message is empty.'
            );
        }

        $commentId = $comment->instagram_comment_id;
        if (! $commentId) {
            throw new \RuntimeException(
                'Instagram comment ID is missing.'
            );
        }

        $result = app(InstagramMessageService::class)
            ->sendPrivateReply(
                instagramAccount: $instagramAccount,
                commentId: $commentId,
                message: $message,
            );

        $context = $run->context ?? [];

        $context['actions'][$action->id] = [
            'type' => AutomationActionType::SEND_MESSAGE->value,
            'message_id' => $result['message_id'] ?? null,
            'recipient_id' => $result['recipient_id'] ?? null,
            'comment_id' => $commentId,
            'sent_at' => now()->toIso8601String(),
        ];

        $run->update(['context' => $context]);

        Log::info('=== AUTOMATION PRIVATE REPLY SUCCESS ===', [
            'run_id' => $run->id,
            'action_id' => $action->id,
            'comment_id' => $commentId,
            'message_id' => $result['message_id'] ?? null,
        ]);
    }
}
