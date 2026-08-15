<?php

namespace Modules\Transactions\Http\Livewire\User;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Transactions\Enums\TransactionStatus;
use Modules\Transactions\Filters\TransactionFilter;
use Modules\Transactions\Services\TransactionService;

class UserTransactionList extends UserBaseComponent
{
    // use Authorizable;
    use LivewireNotify;
    use WithPagination;

    public $columns = [];

    public $activeTab;

    public $sortBy;

    public $allTabs = [];

    protected $queryString = [
        'activeTab' => ['except' => ''],
        'search' => '',
    ];

    public $search;

    public $sortOptions = [];

    public $currency;

    public function mount()
    {
        $this->currency = app(SettingHelper::class)->currencyLabel();

        $this->sortOptions = [
            'created_at:desc' => __('core::attributes.newest'),
            'created_at:asc' => __('core::attributes.oldest'),
        ];
    }

    #[On('sortByChanged')]
    public function changeSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
        $this->resetPage();
    }

    public $filterData;

    #[On('updateTransactionListFilters')]
    public function handleFilters($filters)
    {
        $this->filterData = $filters;
    }

    public function render(TransactionService $transactionService)
    {
        $request = new Request($this->filterData ?? []);
        $filter = new TransactionFilter($request);

        $conditions = [
            'whereHas' => ['order' => function ($q) {
                $q->where('user_id', auth()->id());
            }],
            'where' => ['status' => ['!=', TransactionStatus::PENDING->value]],
        ];
        $transactions = $transactionService->list(
            $this->sortBy ?? null,
            [10, true],
            ['order'],
            conditions: $conditions,
            filter: $filter
        );

        return $this->renderView(
            'Transactions::livewire.user.transactions.transaction-list',
            compact('transactions')
        )->layoutData([
            'title' => __('transaction::attributes.transactions'),
        ]);
    }
}
