<?php

namespace Modules\Instagram\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Instagram\Entities\AutomationRule;
use Modules\Instagram\External\Repositories\Contract\AutomationRuleRepositoryInterface;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Services\TenantService;
use Modules\User\Enums\UserLevel;

class AutomationRuleService
{
    public function __construct(
        protected AutomationRuleRepositoryInterface $AutomationRuleRepository
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->AutomationRuleRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function firstOrCreate(array $conditions, array $data)
    {
        return $this->AutomationRuleRepository->firstOrCreate($conditions, $data);
    }

    public function create(array $data): AutomationRule
    {
        $relations = $this->validateRelations($data);

        $this->authorizeTenant($relations['tenant']);

        $data['tenant_id'] = $relations['tenant']->id;
        $data['instagram_account_id'] = $relations['instagramAccount']->id;
        $data['instagram_post_id'] = $relations['post']?->id;

        return DB::transaction(function () use ($data) {
            $automationRule = $this->AutomationRuleRepository->create($data);

            return $automationRule;
        });
    }

    public function updateOrCreate(array $condition, array $data)
    {
        return $this->AutomationRuleRepository->updateOrCreate($condition, $data);
    }

    public function update(AutomationRule $automationRule, array $data): AutomationRule
    {
        return DB::transaction(function () use ($automationRule, $data) {
            $automationRule = $this->AutomationRuleRepository->update($automationRule, $data);

            return $automationRule;
        });
    }

    private function validateRelations(array $data): array
    {
        $tenant = app(TenantService::class)->findByColumn('slug', $data['tenant']);
        if (! $tenant) {
            throw new \DomainException('Tenant not found.');
        }

        $instagramAccount = app(InstagramAccountService::class)->findByColumn('unique_code', $data['instagram_account']);
        if (! $instagramAccount) {
            throw new \DomainException('Instagram account not found.');
        }

        if ((int) $instagramAccount->tenant_id !== (int) $tenant->id) {
            throw new \DomainException('Instagram account does not belong to selected tenant.');
        }

        $post = null;

        if (! empty($data['instagram_post'])) {
            $post = app(InstagramPostService::class)->findByColumn('id', $data['instagram_post']);
            if (! $post) {
                throw new \DomainException('Instagram post not found.');
            }
            if ((int) $post->instagram_account_id !== (int) $instagramAccount->id) {
                throw new \DomainException('Instagram post does not belong to selected Instagram account.');
            }
        }

        return [
            'tenant' => $tenant,
            'instagramAccount' => $instagramAccount,
            'post' => $post,
        ];
    }

    private function authorizeTenant(Tenant $tenant): void
    {
        $user = auth()->user();

        if ($user->level != UserLevel::ADMIN->value) {
            $hasAccess = $tenant->users()
                ->where('users.id', $user->id)
                ->exists();

            if (! $hasAccess) {
                throw new AuthorizationException(
                    'You do not have access to this tenant.'
                );
            }
        }
    }
}
