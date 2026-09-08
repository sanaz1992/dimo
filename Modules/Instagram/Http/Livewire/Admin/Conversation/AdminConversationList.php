<?php

namespace Modules\Instagram\Http\Livewire\Admin\Conversation;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Instagram\Filters\ConversationFilter;
use Modules\Instagram\Services\ConversationService;
use Modules\Instagram\Services\InstagramAccountService;
use Modules\Instagram\Services\MessageService;

class AdminConversationList extends AdminBaseComponent
{
    use Authorizable;
    use LivewireNotify;
    use WithPagination;

    public ?string $account = null;

    public array $filterData = [];

    public $selectedConversation = null;

    public array $messages = [];

    public $instagramAccount = null;

    protected $queryString = [
        'tenant',
        'account',
    ];

    public function mount(): void
    {
        $this->authorize('conversations_list');
    }

    #[On('updateConversationListFilters')]
    public function handleFilters(array $filters): void
    {
        $this->filterData = $filters;
        $this->resetPage();
    }

    private function fillFilterData(): void
    {
        foreach ($this->queryString as $filter) {
            if (! empty($this->{$filter})) {
                $this->filterData[$filter] ??= $this->{$filter};
            }
        }
    }

    public function selectConversation(
        int $id,
        ConversationService $conversationService,
        MessageService $messageService
    ): void {
        $conversation = $conversationService->findByColumn('id', $id);
        if (! $conversation) {
            $this->notify('error', __('instagram::messages.the_requested_conversation_was_not_found'));

            return;
        }
        $conversation->load('instagramAccount');
        $this->selectedConversation = $conversation;
        $this->loadConversationMessages($conversation->id, $messageService);

        $this->dispatch('conversation-selected');
    }

    private function loadConversationMessages(int $conversationId, MessageService $messageService): void
    {
        $this->messages = $messageService->list(
            'created_at:desc',
            conditions: [
                'where' => ['conversation_id' => ['=', $conversationId]],
            ]
        );
    }

    public function render(ConversationService $conversationService, InstagramAccountService $instagramAccountService)
    {
        $this->fillFilterData();
        $filter = new ConversationFilter(new Request($this->filterData));

        $conversations = $conversationService->list(
            'created_at:desc',
            [10, true],
            with: ['instagramAccount'],
            filter: $filter
        );

        $this->instagramAccount = null;

        if (! empty($this->filterData['account'])) {
            $this->instagramAccount = $instagramAccountService->findByColumn('unique_code', $this->filterData['account']);
        }

        return $this->renderView(
            'Instagram::livewire.admin.conversations.conversation-list',
            compact('conversations')
        )->layoutData([
            'title' => __('instagram::attributes.conversation_list'),
        ]);
    }
}
