<?php

namespace Modules\Transactions\Http\Livewire\Admin;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Transactions\Filters\TransactionFilter;
use Modules\Transactions\Services\TransactionService;

class TransactionList extends AdminBaseComponent
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
        // $this->authorize('orders_list');

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

        $transactions = $transactionService->list(
            $this->sortBy ?? null,
            [10, true],
            ['order'],
            filter: $filter
        );

        return $this->renderView(
            'Transactions::livewire.admin.transactions.transaction-list',
            compact('transactions')
        )->layoutData([
            'title' => __('transaction::attributes.transactions'),
        ]);
    }
}
