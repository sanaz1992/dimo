<?php

namespace Modules\Instagram\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Modules\Instagram\Entities\AutomationAction;
use Modules\Instagram\External\Repositories\Contract\AutomationActionRepositoryInterface;
use Modules\Tenant\Services\TenantService;
use Modules\User\Enums\UserLevel;

class AutomationActionService
{
    public function __construct(
        protected AutomationActionRepositoryInterface $automationActionRepository,
        protected AutomationRuleService $automationRuleService,
        protected TenantService $tenantService
    ) {}

    public function findByColumn($col, $value)
    {
        return $this->automationActionRepository->findByColumn($col, $value);
    }

    public function create(array $data): AutomationAction
    {
        $automationRule = $this->automationRuleService->findByColumn('id', $data['automation_rule_id']);
        if (! $automationRule) {
            throw new \DomainException('Automation rule not found.');
        }

        $this->authorizeAutomationRule($automationRule);

        $data['automation_rule_id'] = $automationRule->id;

        return DB::transaction(function () use ($data) {
            return $this->automationActionRepository->create($data);
        });
    }

    public function update(AutomationAction $automationAction, array $data): AutomationAction
    {
        $automationAction->loadMissing('automationRule');
        if (! $automationAction->automationRule) {
            throw new \DomainException('Automation rule not found.');
        }

        $this->authorizeAutomationRule($automationAction->automationRule);

        return DB::transaction(function () use ($automationAction, $data) {
            return $this->automationActionRepository->update($automationAction, $data);
        });
    }

    public function delete(AutomationAction $automationAction): bool
    {
        $automationAction->loadMissing('automationRule');

        if (! $automationAction->automationRule) {
            throw new \DomainException('Automation rule not found.');
        }

        $this->authorizeAutomationRule($automationAction->automationRule);

        return $this->automationActionRepository->delete($automationAction->id);
    }

    private function authorizeAutomationRule($automationRule): void
    {
        $user = auth()->user();

        if (! $user) {
            throw new AuthorizationException(
                'Unauthenticated user.'
            );
        }

        if ($user->level == UserLevel::ADMIN->value) {
            return;
        }

        $hasAccess = $this->tenantService->findByColumn('id', $automationRule->tenant_id)
            ?->users()
            ->where('users.id', $user->id)
            ->exists();
        if (! $hasAccess) {
            throw new AuthorizationException('You do not have access to this automation rule.');
        }
    }
}
