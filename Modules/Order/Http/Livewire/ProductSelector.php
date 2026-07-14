<?php

namespace Modules\Order\Http\Livewire;

use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Category\Enums\CategoryType;
use Modules\Category\Services\CategoryService;
use Modules\Core\Helpers\ConvertDatesHelper;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Rules\Mobile;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Services\OrderItemService;
use Modules\Order\Services\OrderService;
use Modules\Product\Services\ProductService;
use Modules\User\Enums\UserLevel;
use Modules\Warehouse\Services\ColorService;
use Modules\Warehouse\Services\FabricModelService;
use Modules\Warehouse\Services\FabricWarehouseService;

class ProductSelector extends Component
{
    use LivewireNotify;

    public $order;

    public $categories = [];

    public $categoryDepth;

    public $products = [];

    public $form = [];

    public $hasFabric = false;

    public $hasFrame = false;

    public $fabricModels = [];

    public $fabrics = [];

    // public $fabricColors = [];
    public $selectedProduct;

    public $frameColors = [];

    public $selectedProducts = [];

    public $count = 0;

    public $selectedFabricModel;

    public function mount($order = null)
    {
        $this->order = $order;
        $this->initOrderForm();
    }

    public $currency;

    public $currentUser;

    public function initOrderForm()
    {
        $this->currentUser = auth()->user();
        $this->categoryDepth = (int) (app(SettingHelper::class)->setting('max_product_category_depth')?->value ?? 1);

        $this->currency = app(SettingHelper::class)->currencyLabel();
        $this->categories[0] = resolve(CategoryService::class)->list(
            conditions: [
                'where' => ['type' => ['=', CategoryType::PRODUCT->value]],
                'whereNull' => ['parent_id'],
            ]
        );
        $this->form = [
            'code' => '',
            'customer_name' => '',
            'customer_mobile' => '',
            'description' => '',
            'category' => '',
            'product_id' => '',
            'send_fabric_by_customer' => false,
            'custom_frame' => false,
            'qty' => '',
            'fabrics' => [],
            'product_code' => '',
            'product_title' => '',
            'product_price' => '',
            'categories' => [],
        ];
        if (isset($this->order)) {
            $this->form['code'] = $this->order->code;
        }
        if (isset($this->order->user)) {
            $this->form['customer_name'] = $this->order->user->name;
            $this->form['customer_mobile'] = $this->order->user->mobile;
        }
        $this->frameColors = resolve(ColorService::class)->list(null, [], [], [
            'where' => ['is_frame_color' => ['=', true]],
        ]);
    }

    public function resetForm(array $ignore = [])
    {
        $defaults = [
            'category' => '',
            'product_id' => '',
            'send_fabric_by_customer' => false,
            'custom_frame' => false,
            'qty' => '',
            'fabric_models' => [],
            'fabrics' => [],
            'product_code' => '',
            'product_title' => '',
            'product_price' => '',
            'fabric_id' => '',
            'color_id' => '',
        ];

        foreach ($defaults as $key => $value) {
            if (! in_array($key, $ignore)) {
                $this->form[$key] = $value;
            }
        }
    }

    public $selectedText = [];

    public function changeCategory($level, $value)
    {
        $this->form['categories'][$level] = $value;
        // if (blank($value)) {
        $this->categories[$level + 1] = resolve(CategoryService::class)->list(conditions: [
            'where' => [
                'parent_id' => ['=', $value],
                'type' => ['=', CategoryType::PRODUCT->value],
            ],
        ]);
        $this->selectedText[$level] = $this->categories[$level]->where('id', $value)->first()->title;
        $this->selectedText[$level + 1] = '';
        for ($i = $level + 2; $i < $this->categoryDepth; $i++) {
            $this->categories[$i] = [];
            $this->selectedText[$i] = '';
        }
        if ($level + 1 == $this->categoryDepth) {
            // $this->form['category'] = $value;
            if (blank($value)) {
                $this->products = [];
                $this->resetForm(['category']);
                $this->hasFabric = false;
                $this->hasFrame = false;

                return;
            }

            $conditions = [
                'where' => [
                    'category_id' => ['=', $value],
                    'is_publish' => ['=', true],
                ],
            ];
            $this->products = resolve(ProductService::class)->list(null, [], [], $conditions);

            $this->resetForm(['category']);
            $this->hasFabric = false;
            $this->hasFrame = false;
        }

        $conditions = [
            'where' => [
                'category_id' => ['=', $value],
                'is_publish' => ['=', true],
                'is_intermediate' => ['=', false],
            ],
        ];
        $this->products = resolve(ProductService::class)->list(null, [], [], $conditions);

        $this->resetForm(['category']);
        $this->hasFabric = false;
        $this->hasFrame = false;
    }

    public function changeProduct($value)
    {
        $this->form['product_id'] = $value;
        if (blank($value)) {
            $this->hasFabric = false;
            $this->hasFrame = false;
            $this->selectedProduct = null;
            $this->resetForm(['category', 'product_id']);

            return;
        }

        $product = resolve(ProductService::class)->find($value);
        $this->hasFabric = $product->has_fabric;
        $this->hasFrame = $product->has_frame;
        $this->selectedProduct = $product->load('required_fabrics');
        if ($this->hasFabric) {
            $this->fabricModels = resolve(FabricModelService::class)->list(with: ['fabrics']);
            // $this->fabrics = resolve(FabricWarehouseService::class)->list();
            $this->form['fabrics'] = [];

            foreach ($product->required_fabrics as $requiredFabric) {
                $this->form['fabrics'][$requiredFabric->id]['fabric_qty'] = $requiredFabric->qty;
                $this->form['fabrics'][$requiredFabric->id]['required_fabric_title'] = $requiredFabric->title;
            }
        }

        $this->resetForm(['category', 'product_id', 'fabrics']);
    }

    public function changeFabricModel($requiredProductFabricId, $id)
    {
        $this->form['fabrics'][$requiredProductFabricId]['fabric_model_id'] = $id;
        $this->form['fabrics'][$requiredProductFabricId]['fabric_id'] = '';
        if (blank($id)) {
            return;
        }

        $this->fabrics[$id] = resolve(FabricWarehouseService::class)->list(conditions: [
            'where' => ['fabric_model_id' => ['=', $id]],
        ]);
    }

    public function changeSendFabricByCustomer()
    {
        $this->hasFabric = ! $this->form['send_fabric_by_customer'];
        if ($this->form['send_fabric_by_customer']) {
            $this->resetForm(['category', 'product_id', 'send_fabric_by_customer', 'qty']);
        }
    }

    public function changeFabric($requiredFabricId, $value)
    {
        $fabric = resolve(FabricWarehouseService::class)->find($value);
        // $fabric->load('colors');
        $this->form['fabrics'][$requiredFabricId]['fabric_id'] = $fabric->id;
        $this->form['fabrics'][$requiredFabricId]['fabric_title'] = $fabric->title;
        // $this->fabricColors[$requiredFabricId] = $fabric->colors;
    }

    public function addProductToOrder()
    {
        $this->form['code'] = ConvertDatesHelper::convertPersianNumbersToEnglish($this->form['code']);
        $this->form['customer_mobile'] = ConvertDatesHelper::convertPersianNumbersToEnglish($this->form['customer_mobile']);
        $this->form['qty'] = ConvertDatesHelper::convertPersianNumbersToEnglish($this->form['qty']);

        $rules = [
            'form.code' => [
                'nullable',
                'string',
                'max:255',
                $this->order
                    ? Rule::unique('orders', 'code')->ignore($this->order->id)
                    : Rule::unique('orders', 'code'),
            ],
            'form.customer_name' => 'nullable|string|max:255',
            'form.customer_mobile' => [
                'nullable',
                'string',
                'size:11',
                new Mobile,
                $this->order && $this->order->user_id
                    ? Rule::unique('users', 'mobile')->ignore($this->order->user_id)
                    : Rule::unique('users', 'mobile'),
            ],
            // 'form.category' => 'required|exists:categories,id',
            'form.product_id' => 'required|exists:products,id',
            'form.send_fabric_by_customer' => 'sometimes|boolean',
            'form.qty' => 'required|numeric|min:1',
            'form.categories' => 'required|array|min:1',
        ];

        foreach (range(0, $this->categoryDepth - 1) as $level) {
            // if (isset($this->categories[$level]) && count($this->categories[$level])) {
            $rules["form.categories.$level"] = 'required|exists:categories,id';
            // }
        }

        if ($this->hasFrame) {
            $rules = array_merge($rules, [
                'form.custom_frame' => 'sometimes|boolean',
                'form.frame_color_id' => 'required|exists:colors,id',
            ]);
            // $this->validate(
            //     [

            //     ],
            //     trans('order::validation'),
            //     trans('order::attributes')
            // );
        }
        if ($this->hasFabric) {
            $rules = array_merge($rules, [
                'form.fabrics' => 'required|array',
                'form.fabrics.*.fabric_id' => 'required|exists:fabric_warehouse,id',
                'form.fabrics.*.fabric_model_id' => 'required|exists:fabrics_model,id',
            ]);
            // $this->validate(
            //     [

            //         // 'form.fabrics.*.fabric_title' => 'required|string|max:255',
            //         // 'form.fabrics.*.color_id' => 'required|exists:colors,id',
            //     ],
            //     trans('order::validation'),
            //     trans('order::attributes')
            // );
        }
        $this->validate(
            $rules,
            trans('order::validation'),
            trans('order::attributes')
        );
        try {
            $redirect = false;

            if (! isset($this->order)) {
                $this->order = resolve(OrderService::class)->create($this->form);
                $this->dispatch('first-product-added', $this->order->id);
                $this->order->refresh();
            }
            $orderItem = resolve(OrderItemService::class)
                ->createOrderItem($this->order, $this->form);
            if ($redirect) {
                $this->notify('success', __('core::messages.create.success'));
                if (auth()->user()->level == UserLevel::ADMIN->value) {
                    $redirectRoute = 'admin.orders.edit';
                } else {
                    $redirectRoute = 'seller.orders.edit';
                }

                return redirect()->route($redirectRoute, ['order' => $this->order]);
            }
            if (! isset($this->order)) {
                $this->order = resolve(OrderService::class)->find($orderItem->order_id);
            }
            $this->dispatch('order-items-updated')->to(ProductsTable::class);
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
        // $this->resetForm();
        // $this->form['qty'] = '';
        // $this->form['custom_frame'] = false;
        // $this->products = [];
        // // $this->hasFrameColor = false;
        // // $this->frameColors = [];
        // $this->hasFabric = false;
        // $this->fabrics = [];
        // // $this->fabricColors = [];
    }

    #[On('request-customer-data')]
    public function sendCustomerData()
    {
        $this->form['code'] = ConvertDatesHelper::convertPersianNumbersToEnglish($this->form['code']);
        $this->form['customer_mobile'] = ConvertDatesHelper::convertPersianNumbersToEnglish($this->form['customer_mobile']);
        try {
            $rules = [
                'form.code' => [
                    'nullable',
                    'string',
                    'max:255',
                    $this->order
                        ? Rule::unique('orders', 'code')->ignore($this->order->id)
                        : Rule::unique('orders', 'code'),
                ],
                'form.customer_name' => 'required|string|max:255',
                // Mobile is required here: OrderService::update only attaches the
                // customer when a mobile is present, so a missing mobile silently
                // dropped the customer while still reporting success.
                'form.customer_mobile' => [
                    'required',
                    'string',
                    'size:11',
                    new Mobile,
                ],
                'form.description' => 'nullable|string|max:255',
            ];
            $this->validate($rules, trans('order::validation'), trans('order::attributes'));
        } catch (ValidationException $e) {
            // The customer fields sit at the top of the form while the submit
            // button is at the bottom, so surface the failure with a toast and
            // scroll the fields into view — otherwise the inline errors go
            // unnoticed. Inline errors are still shown by re-throwing.
            $this->notify('error', __('order::messages.complete_customer_info'));
            $this->dispatch('scroll-to-customer');
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));

            return;
        }
        $this->dispatch('customer-data-submitted', data: $this->form);
    }

    public function render()
    {
        return view('Order::livewire.order.product-selector');
    }
}
