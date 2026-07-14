<?php

namespace Modules\Product\Http\Livewire\CostItem;

use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Product\Services\CostItemService;

class CostItemList extends AdminBaseComponent
{
    use LivewireNotify;

    // use Authorizable;
    use WithPagination;

    public $sortBy;

    public $filter;

    public $sortOptions = [];

    protected $queryString = [
        'filter',
    ];

    public $selectedItemId;

    public $form = [
        'title' => '',
        'description' => '',
        'is_active' => null,
        'base_price' => 0,
    ];

    public $showCreateModalFlag = false;

    public $selectedItem;

    public $currency;

    public function mount()
    {
        // $this->authorize('warehouse_items_list');

        $this->sortOptions = [
            'created_at:desc' => __('core::attributes.newest'),
            'created_at:asc' => __('core::attributes.oldest'),
            'title:asc' => __('core::attributes.title_asc'),
            'title:desc' => __('core::attributes.title_desc'),
        ];

        $this->currency = app(SettingHelper::class)->currencyLabel();
    }

    #[On('sortByChanged')]
    public function changeSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->form['is_active'] = true;
        $this->showCreateModalFlag = true;
        $this->selectedItem = null;
    }

    public function openUpdateModal($id)
    {
        $this->showCreateModalFlag = true;
        $this->selectedItem = resolve(CostItemService::class)->find($id);
        $this->form['title'] = $this->selectedItem->title;
        $this->form['base_price'] = $this->selectedItem->base_price;
        $this->form['description'] = $this->selectedItem->description;
        $this->form['is_active'] = (bool) $this->selectedItem->is_active;
    }

    public function closeCreateModal()
    {
        $this->showCreateModalFlag = false;
    }

    public function createCostItem(CostItemService $costItemService)
    {
        try {
            $this->validate(
                [
                    'form.title' => 'required|string|max:255',
                    'form.base_price' => 'required|numeric|min:0',
                    'form.description' => 'nullable|string|max:255',
                    'form.is_active' => 'required|bool',
                ],
                trans('product::validation'),
                trans('product::attributes')
            );
            if ($this->selectedItem) {
                $costItem = $costItemService->update($this->selectedItem, $this->form);
            } else {
                $costItem = $costItemService->create($this->form);
            }
        } catch (ValidationException $e) {
            $this->notify('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
        if (isset($costItem)) {
            $this->form['title'] = '';
            $this->resetPage();
            $this->showCreateModalFlag = false;
        }
    }

    public function deleteCostItem($id)
    {
        try {
            resolve(CostItemService::class)->delete($id);
            $this->notify('success', __('core::messages.destroy.success'));
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.destroy.error').$e->getMessage());
        }
    }

    public function render(CostItemService $costItemService)
    {

        $items = $costItemService->list(
            $this->sortBy ?? null,
            [10, true],
        );

        return $this->renderView(
            'Product::livewire.cost-item.cost-item-list',
            compact('items')
        )->layoutData([
            'title' => __('product::attributes.cost_items'),
        ]);
    }
}
