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
        $conditions = [
            'where' => [
                'tenant_id' => ['=', $comment->instagramAccount->tenant_id],
                'instagram_account_id' => ['=', $comment->instagram_account_id],
                'is_active' => ['=', true],
                function ($query) use ($comment) {
                    $query->whereNull('instagram_post_id')
                        ->orWhere('instagram_post_id', $comment->instagram_post_id);
                },
            ],
        ];

        $rules = app(AutomationRuleService::class)->list('priority', conditions: $conditions);

        $automationRunService = app(AutomationRunService::class);

        foreach ($rules as $rule) {
            if (! $this->matches($rule, $comment)) {
                continue;
            }

            $existingRun = $automationRunService->list(
                conditions: [
                    'where' => [
                        'automation_rule_id' => ['=', $rule->id],
                        'instagram_comment_id' => ['=', $comment->id],
                    ],
                ]
            )->first();

            // if automation executed complately, don't do it again
            if ($existingRun && $existingRun->status === AutomationRunStatus::COMPLETED) {
                continue;
            }

            // if run exists but failed or processing/pending, continue with the same run
            if ($existingRun) {
                $this->executeRun($existingRun);

                continue;
            }

            // first run, create a new AutomationRun record and execute it
            $run = $automationRunService->create([
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
        $automationRunService = app(AutomationRunService::class);

        $run = $automationRunService->update($run, [
            'status' => AutomationRunStatus::PROCESSING->value,
            'started_at' => $run->started_at ?? now(),
            'error' => null,
            'completed_at' => null,
        ]);

        try {
            $actions = $run->automationRule
                ->actions()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            foreach ($actions as $action) {

                // if this action run successfully before, don't run it again in retry
                $context = $run->context ?? [];

                if (isset($context['actions'][$action->id]) && ($context['actions'][$action->id]['status'] ?? null) === 'completed') {
                    continue;
                }

                match ($action->action_type) {
                    AutomationActionType::SEND_PRIVATE_REPLY => $this->executePrivateReply($run, $action),
                    AutomationActionType::SEND_MESSAGE => $this->executeSendMessage($run, $action),
                    default => Log::warning(
                        'Unsupported automation action',
                        [
                            'run_id' => $run->id,
                            'action_id' => $action->id,
                            'action_type' => $action->action_type->value,
                        ]
                    ),
                };

                // maybe refresh the run to get the latest context after executing the action
                $run->refresh();
            }

            $automationRunService->update($run, [
                'status' => AutomationRunStatus::COMPLETED->value,
                'completed_at' => now(),
                'error' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error(
                '=== Instagram automation run failed ===',
                [
                    'run_id' => $run->id,
                    'error' => $e->getMessage(),
                ]
            );

            $automationRunService->update($run, [
                'status' => AutomationRunStatus::FAILED->value,
                'error' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    private function executePrivateReply(AutomationRun $run, AutomationAction $action): void
    {
        $comment = $run->instagramComment;
        if (! $comment) {
            throw new \RuntimeException('Instagram comment not found for automation run.');
        }

        $instagramAccount = $run->instagramAccount;
        if (! $instagramAccount) {
            throw new \RuntimeException('Instagram account not found for automation run.');
        }

        $message = trim($action->config['message'] ?? '');
        if ($message === '') {
            throw new \RuntimeException('Automation message is empty.');
        }

        $commentId = $comment->instagram_comment_id;
        if (! $commentId) {
            throw new \RuntimeException('Instagram comment ID is missing.');
        }

        $result = app(InstagramMessageService::class)
            ->sendPrivateReply(
                instagramAccount: $instagramAccount,
                commentId: $commentId,
                message: $message,
            );

        $this->storeActionContext(
            run: $run,
            action: $action,
            data: [
                'type' => AutomationActionType::SEND_PRIVATE_REPLY->value,
                'message_id' => $result['message_id'] ?? null,
                'recipient_id' => $result['recipient_id'] ?? null,
                'comment_id' => $commentId,
                'sent_at' => now()->toIso8601String(),
            ]
        );

        Log::info('=== AUTOMATION PRIVATE REPLY SUCCESS ===', [
            'run_id' => $run->id,
            'action_id' => $action->id,
            'comment_id' => $commentId,
            'message_id' => $result['message_id'] ?? null,
        ]);
    }

    private function executeSendMessage(AutomationRun $run, AutomationAction $action): void
    {
        $comment = $run->instagramComment;
        if (! $comment) {
            throw new \RuntimeException('Instagram comment not found for automation run.');
        }

        $instagramAccount = $run->instagramAccount;
        if (! $instagramAccount) {
            throw new \RuntimeException('Instagram account not found for automation run.');
        }

        $message = trim($action->config['message'] ?? '');
        if ($message === '') {
            throw new \RuntimeException('Automation message is empty.');
        }

        $recipientIgId = $comment->commenter_ig_id;
        if (! $recipientIgId) {
            throw new \RuntimeException('Instagram commenter ID is missing.');
        }

        $result = app(InstagramMessageService::class)
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

        Log::info('=== AUTOMATION MESSAGE SUCCESS ===', [
            'run_id' => $run->id,
            'action_id' => $action->id,
            'recipient_id' => $recipientIgId,
            'message_id' => $result['message_id'] ?? null,
        ]);
    }

    private function storeActionContext(AutomationRun $run, AutomationAction $action, array $data): void
    {
        $context = $run->context ?? [];
        $context['actions'][$action->id] = array_merge($data, ['status' => 'completed']);
        app(AutomationRunService::class)->update($run, ['context' => $context]);
    }
}
