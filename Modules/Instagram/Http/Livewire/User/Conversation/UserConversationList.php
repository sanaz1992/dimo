<?php

namespace Modules\Instagram\Http\Livewire\User\Conversation;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Filters\ConversationFilter;
use Modules\Instagram\Services\ConversationService;
use Modules\Instagram\Services\InstagramAccountService;
use Modules\Instagram\Services\InstagramMessageService;
use Modules\Instagram\Services\MessageService;

class UserConversationList extends UserBaseComponent
{
    use LivewireNotify;
    use WithPagination;

    public string $accountUniqueCode;

    public $filterData = [];

    protected $queryString = [
        'tenant',
    ];

    public $selectedConversation;

    public $messages = [];

    public $instagramAccount;

    public string $messageText = '';

    public function mount(string $account): void
    {
        $this->accountUniqueCode = $account;
        $this->instagramAccount = app(InstagramAccountService::class)->findByColumn('unique_code', $account);
    }

    #[On('updateConversationListFilters')]
    public function handleFilters($filters)
    {
        $this->filterData = $filters;
        $this->resetPage();
    }

    public function fillFilterData()
    {
        // $queryFilters = [
        //     'tenant',
        //     // 'status',
        // ];
        $queryFilters = $this->queryString;

        foreach ($queryFilters as $filter) {
            if (! empty($this->{$filter})) {
                $this->filterData[$filter] ??= $this->{$filter};
            }
        }
    }

    public function selectConversation($id)
    {
        $this->selectedConversation = app(ConversationService::class)->findByColumn('id', $id);
        $this->selectedConversation->load('instagramAccount');
        $this->resetConversationMessages($id);
        $this->dispatch('conversation-selected');
    }

    public function resetConversationMessages($conversationId)
    {
        $this->messages = app(MessageService::class)->list(
            'created_at:desc',
            conditions: [
                'where' => ['conversation_id' => ['=', $conversationId]],
            ]
        );
    }

    protected function validateMessage()
    {
        $this->validate(
            [
                'messageText' => ['required', 'string', 'max:2000'],
            ],
            trans('instagram::validation'),
            trans('instagram::attributes')
        );
    }

    public function sendMessage(): void
    {
        $this->validateMessage();

        // مرحله بعد:
        // ارسال پیام از طریق InstagramMessageService

        $result = app(InstagramMessageService::class)->sendTextMessage(
            instagramAccount: $this->instagramAccount,
            recipientIgId: $this->selectedConversation->customer_ig_id,
            message: $this->messageText,
        );

        $this->resetConversationMessages($this->selectedConversation->id);

        $this->reset('messageText');
    }

    public function render(ConversationService $conversationService)
    {
        $this->fillFilterData();
        $this->filterData['user'] = auth()->user()->unique_code;
        $request = new Request($this->filterData ?? []);
        $filter = new ConversationFilter($request);
        $conditions = [
            'whereHas' => ['instagramAccount' => function ($q) {
                $q->where('unique_code', $this->accountUniqueCode);
            }],
        ];

        $conversations = $conversationService->list(
            'created_at:desc',
            [10, true],
            with: ['instagramAccount'],
            conditions: $conditions,
            filter: $filter
        );

        return $this->renderView(
            'Instagram::livewire.user.conversations.conversation-list',
            compact('conversations')
        )
            ->layoutData([
                'title' => __('instagram::attributes.conversation_list'),
            ]);
    }
}
