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
    public function __construct(
        protected AutomationRuleService $automationRuleService,
        protected AutomationRunService $automationRunService,
        protected InstagramMessageService $instagramMessageService,
    ) {}

    public function processComment(InstagramComment $comment): void
    {
        $rules = $this->getMatchingRules($comment);

        foreach ($rules as $rule) {
            if (! $this->matches($rule, $comment)) {
                continue;
            }

            $run = $this->getOrCreateRun($rule, $comment);
            if ($run->status === AutomationRunStatus::COMPLETED) {
                continue;
            }

            $this->executeRun($run);
        }
    }

    private function getMatchingRules(InstagramComment $comment)
    {
        return $this->automationRuleService->list(
            'priority',
            conditions: [
                'where' => [
                    'tenant_id' => ['=', $comment->instagramAccount->tenant_id],
                    'instagram_account_id' => ['=', $comment->instagram_account_id],
                    'is_active' => ['=', true],
                    function ($query) use ($comment) {
                        $query->whereNull('instagram_post_id')
                            ->orWhere('instagram_post_id', $comment->instagram_post_id);
                    },
                ],
            ]
        );
    }

    private function getOrCreateRun(AutomationRule $rule, InstagramComment $comment): AutomationRun
    {
        $existingRun = $this->automationRunService
            ->list(
                conditions: [
                    'where' => [
                        'automation_rule_id' => ['=', $rule->id],
                        'instagram_comment_id' => ['=', $comment->id],
                    ],
                ]
            )->first();
        if ($existingRun) {
            return $existingRun;
        }

        return $this->automationRunService->create([
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
    }

    private function matches(AutomationRule $rule, InstagramComment $comment): bool
    {
        $commentText = trim(mb_strtolower($comment->comment_text ?? ''));
        $matchValue = trim(mb_strtolower($rule->match_value ?? ''));

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
        $run = $this->automationRunService->update($run, [
            'status' => AutomationRunStatus::PROCESSING->value,
            'started_at' => $run->started_at ?? now(),
            'error' => null,
            'completed_at' => null,
        ]);

        try {
            $actions = $this->getActiveActions($run);

            foreach ($actions as $action) {
                if ($this->actionAlreadyCompleted($run, $action)) {
                    continue;
                }

                $this->executeAction($run, $action);

                $run->refresh();
            }

            $this->automationRunService->update($run, [
                'status' => AutomationRunStatus::COMPLETED->value,
                'completed_at' => now(),
                'error' => null,
            ]);
        } catch (\Throwable $e) {
            $this->markRunAsFailed($run, $e);
            throw $e;
        }
    }

    private function getActiveActions(AutomationRun $run)
    {
        return $run->automationRule
            ->actions()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    private function actionAlreadyCompleted(AutomationRun $run, AutomationAction $action): bool
    {
        $context = $run->context ?? [];

        return isset($context['actions'][$action->id])
            && ($context['actions'][$action->id]['status'] ?? null)
            === 'completed';
    }

    private function executeAction(AutomationRun $run, AutomationAction $action): void
    {
        match ($action->action_type) {
            AutomationActionType::SEND_PRIVATE_REPLY => $this->executePrivateReply($run, $action),
            AutomationActionType::SEND_MESSAGE => $this->executeSendMessage($run, $action),
            default => $this->logUnsupportedAction($run, $action),
        };
    }

    private function executePrivateReply(AutomationRun $run, AutomationAction $action): void
    {
        $comment = $this->getComment($run);
        $instagramAccount = $this->getInstagramAccount($run);
        $message = $this->getActionMessage($action);

        $commentId = $comment->instagram_comment_id;
        if (! $commentId) {
            throw new \RuntimeException('Instagram comment ID is missing.');
        }

        if (! $comment->commenter_ig_id) {
            throw new \RuntimeException('Instagram commenter ID is missing.');
        }

        $result = $this->instagramMessageService
            ->sendPrivateReply(
                instagramAccount: $instagramAccount,
                commentId: $commentId,
                recipientIgId: $comment->commenter_ig_id,
                recipientUsername: $comment->commenter_username,
                message: $message,
            );

        $this->storeActionContext(
            run: $run,
            action: $action,
            data: [
                'type' => AutomationActionType::SEND_PRIVATE_REPLY->value,
                'message_id' => $result['message_id'] ?? null,
                'recipient_id' => $result['recipient_id'] ?? $comment->commenter_ig_id,
                'comment_id' => $commentId,
                'sent_at' => now()->toIso8601String(),
            ]
        );

        Log::info(
            'Instagram automation private reply sent.',
            [
                'run_id' => $run->id,
                'action_id' => $action->id,
                'comment_id' => $commentId,
                'message_id' => $result['message_id'] ?? null,
            ]
        );
    }

    private function executeSendMessage(AutomationRun $run, AutomationAction $action): void
    {
        $comment = $this->getComment($run);
        $instagramAccount = $this->getInstagramAccount($run);
        $message = $this->getActionMessage($action);

        $recipientIgId = $comment->commenter_ig_id;
        if (! $recipientIgId) {
            throw new \RuntimeException('Instagram commenter ID is missing.');
        }

        $result = $this->instagramMessageService
            ->sendTextMessage(
                instagramAccount: $instagramAccount,
                recipientIgId: $recipientIgId,
                message: $message,
            );

        $this->storeActionContext(
            run: $run,
            action: $action,
            data: [
                'type' => AutomationActionType::SEND_MESSAGE->value,
                'message_id' => $result['message_id'] ?? null,
                'recipient_id' => $recipientIgId,
                'sent_at' => now()->toIso8601String(),
            ]
        );

        Log::info(
            'Instagram automation message sent.',
            [
                'run_id' => $run->id,
                'action_id' => $action->id,
                'recipient_id' => $recipientIgId,
                'message_id' => $result['message_id'] ?? null,
            ]
        );
    }

    private function getComment(AutomationRun $run): InstagramComment
    {
        $comment = $run->instagramComment;

        if (! $comment) {
            throw new \RuntimeException('Instagram comment not found for automation run.');
        }

        return $comment;
    }

    private function getInstagramAccount(AutomationRun $run)
    {
        $instagramAccount = $run->instagramAccount;
        if (! $instagramAccount) {
            throw new \RuntimeException(
                'Instagram account not found for automation run.'
            );
        }

        return $instagramAccount;
    }

    private function getActionMessage(AutomationAction $action): string
    {
        $message = trim($action->config['message'] ?? '');
        if ($message === '') {
            throw new \RuntimeException('Automation message is empty.');
        }

        return $message;
    }

    private function storeActionContext(AutomationRun $run, AutomationAction $action, array $data): void
    {
        $context = $run->context ?? [];
        $context['actions'][$action->id] = array_merge($data, ['status' => 'completed']);

        $this->automationRunService->update($run, ['context' => $context]);
    }

    private function markRunAsFailed(AutomationRun $run, \Throwable $exception): void
    {
        Log::error(
            'Instagram automation run failed.',
            [
                'run_id' => $run->id,
                'error' => $exception->getMessage(),
            ]
        );

        $this->automationRunService->update(
            $run,
            [
                'status' => AutomationRunStatus::FAILED->value,
                'error' => $exception->getMessage(),
                'completed_at' => now(),
            ]
        );
    }

    private function logUnsupportedAction(AutomationRun $run, AutomationAction $action): void
    {
        Log::warning(
            'Unsupported automation action.',
            [
                'run_id' => $run->id,
                'action_id' => $action->id,
                'action_type' => $action->action_type->value,
            ]
        );
    }
}
