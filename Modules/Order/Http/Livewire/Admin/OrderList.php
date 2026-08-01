<?php

namespace Modules\Order\Http\Livewire\Admin;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Filters\OrderFilter;
use Modules\Order\Services\OrderService;

class OrderList extends AdminBaseComponent
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

    #[On('updateOrderListFilters')]
    public function handleFilters($filters)
    {
        $this->filterData = $filters;
    }

    public function render(OrderService $orderService)
    {
        $request = new Request($this->filterData ?? []);
        $filter = new OrderFilter($request);

        $orders = $orderService->list(
            $this->sortBy ?? null,
            [10, true],
            filter: $filter
        );

        return $this->renderView(
            'Order::livewire.admin.order.order-list',
            compact('orders')
        )->layoutData([
            'title' => __('order::attributes.orders'),
        ]);
    }
}
