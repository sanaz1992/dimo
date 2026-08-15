<?php

namespace Modules\Order\Http\Livewire\User;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Filters\OrderFilter;
use Modules\Order\Services\OrderService;

class UserOrderList extends UserBaseComponent
{
    use WithPagination;

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

    #[On('updateOrderListFilters')]
    public function handleFilters($filters)
    {
        $this->filterData = $filters;
    }

    public function render(OrderService $orderService)
    {
        $request = new Request($this->filterData ?? []);
        $filter = new OrderFilter($request);

        $conditions = [
            'where' => ['user_id' => ['=', auth()->id()]],
            'whereNotIn' => ['status' => [[OrderStatus::DRAFT->value, OrderStatus::EXPIRED->value]]],
        ];
        $orders = $orderService->list(
            $this->sortBy ?? null,
            [10, true],
            conditions: $conditions,
            filter: $filter
        );

        return $this->renderView(
            'Order::livewire.user.order.order-list',
            compact('orders')
        )->layoutData([
            'title' => __('order::attributes.orders'),
        ]);
    }
}
