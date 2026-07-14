<?php

namespace Modules\Product\Http\Livewire\Product;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Category\Enums\CategoryType;
use Modules\Category\Services\CategoryService;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Product\Filters\ProductFilter;
use Modules\Product\Services\ProductService;

class ProductList extends AdminBaseComponent
{
    // use Authorizable;
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
        // $this->authorize('products_list');

        $this->currency = app(SettingHelper::class)->currencyLabel();

        $this->categories = resolve(CategoryService::class)->list(
            orderBy: 'title',
            conditions: [
                'where' => ['type' => ['=', CategoryType::PRODUCT->value]],
            ]
        );
        $this->activeTab = 'all_products';
        $this->allTabs['all_products'] = 'همه محصولات';
        foreach ($this->categories as $category) {
            $this->allTabs[$category->id] = $category->title;
        }
        $this->columns = [
            ['key' => 'image', 'label' => __('product::attributes.image')],
            ['key' => 'code', 'label' => __('product::attributes.code')],
            ['key' => 'title', 'label' => __('product::attributes.title')],
            ['key' => 'category', 'label' => __('product::attributes.category')],
            // ['key' => 'stock', 'label' => __('product::attributes.stock')],
            ['key' => 'price', 'label' => __('product::attributes.price').' ('.$this->currency.')'],
            ['key' => 'status', 'label' => __('product::attributes.status')],
            ['key' => 'actions', 'label' => ''],
        ];
        $this->sortOptions = [
            'created_at:desc' => __('core::attributes.newest'),
            'created_at:asc' => __('core::attributes.oldest'),
            'title:asc' => __('core::attributes.title_asc'),
            'title:desc' => __('core::attributes.title_desc'),
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

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    #[On('tabChanged')]
    public function setTab($categoryId)
    {
        $this->activeTab = $categoryId;
        $this->resetPage(); // refresh category items
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

        $conditions = [
            'where' => ['is_intermediate' => ['=', false]],
        ];
        if ($this->activeTab != 'all_products') {
            $conditions = array_merge($conditions, [
                'where' => ['category_id' => ['=', $this->activeTab]],
            ]);
        }
        // if ($this->search) {
        //     $conditions = array_merge($conditions, [
        //         'where' => [
        //             'title' => ['LIKE', $this->search],
        //         ],
        //         'orWhere' => [
        //             'code' => ['LIKE', $this->search],
        //         ],
        //     ]);
        // }

        $products = $productService->list(
            $this->sortBy ?? null,
            [10, true],
            ['category', 'mainImageRelation', 'cost_items'],
            $conditions,
            $filter
        );

        return $this->renderView(
            'Product::livewire.product.product-list',
            compact('products')
        )->layoutData([
            'title' => __('product::attributes.product_list'),
        ]);
    }
}
