<?php

namespace Modules\Category\Http\Livewire\Category;

use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Modules\Category\Enums\CategoryType;
use Modules\Category\Rules\StoreCategoryRules;
use Modules\Category\Services\CategoryService;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;

class CategoryCreate extends AdminBaseComponent
{
    use WithFileUploads;
    use LivewireNotify;

    public $message;
    public $form = [
        'title' => '',
        'type' => '',
        'image' => null,
        'parent_id' => null
    ];
    public $categories = [];
    public $parents = [];
    public $categoryDepth;
    public function mount()
    {
        // $this->authorize('categories_create');
        $this->categoryDepth = (int) app(SettingHelper::class)->setting('max_product_category_depth')->value;
        $this->categories[0] = resolve(CategoryService::class)->list(conditions: [
            'whereNull' => ['parent_id']
        ]);
    }
    public function updated($name, $value)
    {
        if (str_starts_with($name, 'parents.')) {

            $level = (int) str_replace('parents.', '', $name);

            $this->categories[$level + 1] = resolve(CategoryService::class)->list(conditions: [
                'where' => ['parent_id' => ['=', $value]]
            ]);

            unset($this->parents[$level + 1]);
        }
    }
    public function store(CategoryService $categoryService)
    {
        $parentId = end($this->parents);
        $this->form['parent_id'] = $parentId ?: null;

        try {
            $this->validate(
                StoreCategoryRules::rules(),
                trans('product::validation'),
                trans('product::attributes')
            );

            $categoryService->create($this->form);
            $this->message = __('product::messages.create.success');
            $this->reset('form');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }
    public function render()
    {
        $types = CategoryType::cases();
        return $this->renderView(
            'Category::livewire.category.category-create',
            compact('types')
        )->layoutData([
            'title' => __('product::attributes.category_create')
        ]);
    }
}
