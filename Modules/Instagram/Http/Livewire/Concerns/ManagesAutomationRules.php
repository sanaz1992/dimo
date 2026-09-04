<?php

namespace Modules\Instagram\Http\Livewire\Concerns;

use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Modules\Instagram\Entities\AutomationRule;
use Modules\Instagram\Enums\AutomationMatchType;
use Modules\Instagram\Enums\AutomationTriggerType;
use Modules\Instagram\Rules\StoreAutomationRuleRules;
use Modules\Instagram\Services\AutomationRuleService;
use Modules\Instagram\Services\InstagramAccountService;
use Modules\Instagram\Services\InstagramPostService;

trait ManagesAutomationRules
{
    use WithFileUploads;

    public ?AutomationRule $automationRule = null;

    public array $form = [
        'tenant' => '',
        'instagram_account' => '',
        'instagram_post' => '',
        'name' => '',
        'trigger_type' => '',
        'match_type' => '',
        'match_value' => '',
        'is_active' => '',
        'priority' => '',
    ];

    public array $matchTypes = [];

    public array $triggerTypes = [];

    public $tenants = [];

    public $instagramAccounts = [];

    public $instagramPosts = [];

    public string $currentStep = 'basic';

    public array $steps = [
        'basic' => 'اطلاعات پایه',
        'automation_actions' => 'اقدامات',
    ];

    /**
     * Each panel must determine which tenants
     * are available to the current user.
     */
    abstract protected function getAvailableTenants();

    protected function fillForm(?AutomationRule $automationRule = null): void
    {
        $this->matchTypes = AutomationMatchType::labels();
        $this->triggerTypes = AutomationTriggerType::labels();

        if ($automationRule) {
            $this->automationRule = $automationRule;
            $this->form['tenant'] = $automationRule->tenant->slug;
            $this->form['instagram_account'] = $automationRule->instagramAccount->unique_code;
            $this->form['instagram_post'] = $automationRule->instagramPost?->id ?? '';
            $this->form['name'] = $automationRule->name;
            $this->form['trigger_type'] = $automationRule->trigger_type?->value ?? $automationRule->trigger_type;
            $this->form['match_type'] = $automationRule->match_type?->value ?? $automationRule->match_type;
            $this->form['match_value'] = $automationRule->match_value;
            $this->form['is_active'] = $automationRule->is_active;
            $this->form['priority'] = $automationRule->priority;

            $this->loadInstagramAccounts($this->form['tenant']);

            $this->loadInstagramPosts($this->form['instagram_account']);
        }

        $this->tenants = $this->getAvailableTenants();
    }

    protected function automationRuleRules(): array
    {
        return StoreAutomationRuleRules::rules();
    }

    protected function validateAutomationRule(): void
    {
        $this->validate(
            $this->automationRuleRules(),
            trans('instagram::validation'),
            trans('instagram::attributes')
        );
    }

    protected function createAutomationRule(AutomationRuleService $automationRuleService): AutomationRule
    {
        $data = $this->form;

        return $automationRuleService->create($data);
    }

    protected function updateAutomationRule(AutomationRuleService $automationRuleService): AutomationRule
    {
        return $automationRuleService->update($this->automationRule, $this->form);
    }

    public function updatedFormTenant($tenantSlug): void
    {
        $this->instagramAccounts = [];
        $this->instagramPosts = [];

        $this->form['instagram_account'] = '';
        $this->form['instagram_post'] = '';

        if (! $tenantSlug) {
            return;
        }

        $this->loadInstagramAccounts($tenantSlug);
    }

    public function updatedFormInstagramAccount($accountUniqueCode): void
    {
        $this->instagramPosts = [];
        $this->form['instagram_post'] = '';
        if (! $accountUniqueCode) {
            return;
        }
        $this->loadInstagramPosts($accountUniqueCode);
    }

    protected function loadInstagramAccounts(string $tenantSlug): void
    {
        $this->instagramAccounts = app(InstagramAccountService::class)->list(
            conditions: [
                'whereHas' => [
                    'tenant' => function ($query) use ($tenantSlug) {
                        $query->where('slug', $tenantSlug);
                    },
                ],
            ]
        );
    }

    protected function loadInstagramPosts(string $accountUniqueCode): void
    {
        $posts = app(InstagramPostService::class)->list(
            conditions: [
                'whereHas' => [
                    'instagramAccount' => function ($query) use ($accountUniqueCode) {
                        $query->where(
                            'unique_code',
                            $accountUniqueCode
                        );
                    },
                ],
            ]
        );

        $this->instagramPosts = collect($posts)
            ->mapWithKeys(function ($post) {
                return [$post->id => $this->buildPostLabel($post)];
            })->toArray();
    }

    protected function buildPostLabel($post): string
    {
        $type = $post->media_product_type?->value ?? 'post';

        $date = $post->published_at ? $post->published_at->format('Y/m/d') : '';

        $caption = trim($post->caption ?? '');

        if ($caption !== '') {
            $caption = mb_substr($caption, 0, 50);
            if (mb_strlen($post->caption ?? '') > 50) {
                $caption .= '...';
            }
        }

        return trim("{$type} - {$date} - {$caption}", ' -');
    }

    public function nextStep(AutomationRuleService $automationRuleService): void
    {
        match ($this->currentStep) {
            'basic' => $this->storeAutomationRule($automationRuleService),
            default => $this->showNextStep(),
        };
    }

    public function storeAutomationRule(AutomationRuleService $automationRuleService): void
    {
        try {
            $this->validateAutomationRule();

            $this->createAutomationRule($automationRuleService);

            $this->notify('success', __('core::messages.create.success'));
            $this->reset('form');
            $this->showNextStep();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            dd($e->getMessage());
            $this->notify('error', __('core::messages.create.error'));
        }
    }

    protected function showNextStep(): void
    {
        // Actions step will be implemented later.
    }
}
