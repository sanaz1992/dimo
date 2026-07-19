<?php

namespace Modules\Product\Http\Livewire\Guest\Product;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Services\ProductService;

class ProductList extends AdminBaseComponent
{
    use LivewireNotify;
    use WithPagination;

    public $columns = [];

    public $activeTab;

    public $sortBy;

    public $allTabs = [];

    public $categories;

    protected $queryString = [
        'activeTab' => ['except' => ''],
        'search' => '',
        'category' => '',
        'price_min' => '',
        'price_max' => '',
        'published' => '',
    ];

    public $search;

    public $category;

    public $price_min;

    public $price_max;

    public $published;

    public $sortOptions = [];

    public $currency;

    public function mount()
    {
        dd('GuestProductList');
        // $this->authorize('products_list');

        $this->currency = app(SettingHelper::class)->currencyLabel();

        $this->sortOptions = [
            'created_at:desc' => __('core::attributes.newest'),
            'created_at:asc' => __('core::attributes.oldest'),
            'name:asc' => __('core::attributes.name_asc'),
            'name:desc' => __('core::attributes.name_desc'),
            'price:asc' => __('core::attributes.price_asc'),
            'price:desc' => __('core::attributes.price_desc'),
        ];
    }

    #[On('sortByChanged')]
    public function changeSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
        $this->resetPage();
    }



    public function publishProduct($productId)
    {
        $this->authorize('products_publish');
        try {
            resolve(ProductService::class)->publishProduct($productId);
            $this->notify('success', __('product::messages.change_publish_status.success'));
        } catch (InvalidArgumentException $e) {
            $this->notify('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->notify('error', __('product::messages.change_publish_status.error'));
        }
    }

    public $filterData;

    #[On('updateProductListFilters')]
    public function handleFilters($filters)
    {
        $this->filterData = $filters;
    }

    public function render(ProductService $productService)
    {
        $request = new Request($this->filterData ?? []);
        $filter = new ProductFilter($request);



        $products = $productService->list(
            $this->sortBy ?? null,
            [10, true],
            ['category', 'mainImageRelation'],
            filter: $filter
        );

        return $this->renderView(
            'Product::livewire.admin.product.product-list',
            compact('products')
        )->layoutData([
            'title' => __('product::attributes.products'),
        ]);
    }
}
