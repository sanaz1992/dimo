<?php

namespace Modules\User\Http\Livewire\Admin\Seller;

use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Enums\UserLevel;
use Modules\User\Services\UserService;

class SellerList extends AdminBaseComponent
{
    use WithPagination;
    // use Authorizable;
    use LivewireNotify;

    public $columns = [];
    public $rows = [];
    public $statusConfig = [];
    public $orderActiveStatuses = [];
    protected $listeners = ['refreshList' => '$refresh'];

    public function mount()
    {
        // $this->authorize('sellers_list');

        $this->columns = [
            ['key' => 'avatar', 'label' =>  __('user::attributes.avatar')],
            ['key' => 'name', 'label' =>  __('user::attributes.name')],
            ['key' => 'mobile', 'label' =>  __('user::attributes.mobile')],
            ['key' => 'city', 'label' =>  __('user::attributes.city')],
            ['key' => 'orders_count', 'label' =>  __('user::attributes.orders_count')],
            ['key' => 'orders_active', 'label' =>  __('user::attributes.orders_active')],
            ['key' => 'orders_total_price', 'label' =>  __('user::attributes.orders_total_price') . '(' . __('user::attributes.rial') . ')'],
            ['key' => 'last_activity', 'label' =>  __('user::attributes.last_activity')],
            ['key' => 'status', 'label' =>  __('user::attributes.status')],
            ['key' => 'actions', 'label' => ''],
        ];
    }

    public function confirmDelete()
    {
        $this->dispatchBrowserEvent('show-delete-confirmation');
    }

    public function deleteItem(UserService $userService, $id)
    {
        $userService->delete($id);
        $this->notify('success', __('core::messages.destroy.success'));
        $this->dispatch('refreshList');
    }

    public function render(UserService $userService)
    {
        $conditions = [
            'where' => ['level' => ['=', UserLevel::SELLER->value]],
        ];
        $users = $userService->list(
            null,
            [10, true],
            [
                'mainImageRelation',
                'addresses.city',
            ],
            $conditions
        );

        return $this->renderView('User::livewire.admin.seller.seller-list', compact('users'))
            ->layoutData([
                'title' => __('user::attributes.sellers_list')
            ]);
    }
}
