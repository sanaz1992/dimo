<?php

namespace Modules\Order\Http\Livewire\Admin;

use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Helpers\ConvertDatesHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Enums\OrderListTabs;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Filters\OrderFilter;
use Modules\Order\Services\OrderService;

class OrderList extends AdminBaseComponent
{
    use LivewireNotify;
    use WithPagination;

    public $showFilterModal = false;

    public $sortBy;

    public $activeTab;

    public $tabs;

    // public $tab;
    public $status;

    public $code;

    public $seller;

    public $user_id;

    public $created_at_from;

    public $created_at_to;

    public $started_at_from;

    public $started_at_to;

    public $finished_at_from;

    public $finished_at_to;

    public $price_min;

    public $price_max;

    public $hall;
    // status و seller از قبل هستند

    protected $queryString = [
        'status',
        'code',
        'seller',
        'user_id',
        'created_at_from',
        'created_at_to',
        'started_at_from',
        'started_at_to',
        'finished_at_from',
        'finished_at_to',
        'price_min',
        'price_max',
        'hall',
        'activeTab' => ['except' => ''],
    ];

    public $sortOptions = [];

    public function mount()
    {
        // $this->authorize('orders_list');
        $this->activeTab ??= OrderListTabs::All->value;
        $this->tabs = OrderListTabs::labels();

        $this->sortOptions = [
            'created_at:desc' => __('core::attributes.newest'),
            'created_at:asc' => __('core::attributes.oldest'),
            'total_price:asc' => __('core::attributes.price_asc'),
            'total_price:desc' => __('core::attributes.price_desc'),
        ];
    }

    protected $listeners = [
        'reinitDatepickers' => '$refresh',
    ];

    #[On('updateOrderListFilters')]
    public function handleFilters($filters)
    {
        $this->code = $filters['code'];
        $this->seller = $filters['seller'];
        $this->created_at_from = $filters['created_at_from'];
        $this->created_at_to = $filters['created_at_to'];
        $this->started_at_from = $filters['started_at_from'] ?? null;
        $this->started_at_to = $filters['started_at_to'] ?? null;
        $this->finished_at_from = $filters['finished_at_from'] ?? null;
        $this->finished_at_to = $filters['finished_at_to'] ?? null;
        $this->price_min = $filters['price_min'] ?? null;
        $this->price_max = $filters['price_max'] ?? null;
        $this->hall = $filters['hall'] ?? null;
    }

    #[On('sortByChanged')]
    public function changeSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
        $this->resetPage();
    }

    #[On('tabChanged')]
    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage(); // refresh category items
    }

    public function deleteOrder($id)
    {
        try {
            $result = resolve(OrderService::class)->destroy($id);
            if ($result['status']) {
                $this->notify('success', __('core::messages.destroy.success'));
            } else {
                $this->notify('error', $result['message']);
            }
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.destroy.error'));
        }
    }

    public function render()
    {
        $from = ConvertDatesHelper::convertPersianNumbersToEnglish($this->created_at_from);
        $from = ConvertDatesHelper::jalaliToGregorian($from);
        $to = ConvertDatesHelper::convertPersianNumbersToEnglish($this->created_at_to);
        $to = ConvertDatesHelper::jalaliToGregorian($to);

        $started_from = ConvertDatesHelper::convertPersianNumbersToEnglish($this->started_at_from);
        $started_from = ConvertDatesHelper::jalaliToGregorian($started_from);
        $started_to = ConvertDatesHelper::convertPersianNumbersToEnglish($this->started_at_to);
        $started_to = ConvertDatesHelper::jalaliToGregorian($started_to);

        $finished_from = ConvertDatesHelper::convertPersianNumbersToEnglish($this->finished_at_from);
        $finished_from = ConvertDatesHelper::jalaliToGregorian($finished_from);
        $finished_to = ConvertDatesHelper::convertPersianNumbersToEnglish($this->finished_at_to);
        $finished_to = ConvertDatesHelper::jalaliToGregorian($finished_to);

        $filterData = array_filter([
            // 'status' => $this->status,
            'code' => $this->code,
            'seller' => $this->seller,
            'user' => $this->user_id,
            'created_at_from' => $from,
            'created_at_to' => $to,
            'started_at_from' => $started_from,
            'started_at_to' => $started_to,
            'finished_at_from' => $finished_from,
            'finished_at_to' => $finished_to,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'hall' => $this->hall,
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        $request = new Request($filterData);
        $filter = new OrderFilter($request);

        $conditions = [
            'whereIn' => ['status' => [OrderListTabs::statuses()[$this->activeTab]]],
        ];
        if ($this->activeTab == OrderListTabs::DRAFT->value) {
            array_merge($conditions, [
                'where' => ['seller_id' => ['=', auth()->user()->id]],
            ]);
        }
        if ($this->status) {
            $conditions = array_merge($conditions, [
                'whereIn' => ['status' => [$this->status == 'active-statuses' ? OrderStatus::activeStatuses() : $this->status]],
            ]);
        }

        $orders = resolve(OrderService::class)->list(
            $this->sortBy ?? null,
            [10, true],
            ['seller.addresses.city', 'items'],
            $conditions,
            $filter
        );

        return $this->renderView(
            'Order::livewire.order.admin.order-list',
            compact('orders')
        )->layoutData([
            'title' => __('order::attributes.orders_list'),
        ]);
    }
}
