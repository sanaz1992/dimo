<?php

namespace Modules\Category\Http\Livewire\Category;

use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Category\Entities\Category;
use Modules\Category\Enums\CategoryType;
use Modules\Category\Rules\StoreCategoryRules;
use Modules\Category\Services\CategoryService;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;

class CategoryEdit extends AdminBaseComponent
{
    use LivewireNotify;
    use WithFileUploads;

    public $category;

    public $initialImage;

    public $image;

    public $message;

    public $form = [
        'title' => '',
        'image' => null,
        'parent_id' => '',
        'type' => '',
    ];

    public $categories = [];

    public $parents = [];

    public $categoryDepth;

    public function mount(Category $category)
    {
        // $this->authorize('categories_edit');
        $category->load('mainImageRelation');
        $this->category = $category;
        $this->form['title'] = $category->title;
        $this->form['parent_id'] = $category->parent_id;
        $this->form['type'] = $category->type?->value;
        // dd($this->form);
        $this->initialImage = $category->main_image?->getThumbnailUrl('original');

        $this->categoryDepth = (int) app(SettingHelper::class)->setting('max_product_category_depth')->value;

        $currnetParent = $this->category->parent;

        if ($currnetParent === null) {
            // Root category (no parent): only show the top-level dropdown
            // with root categories and leave the parent unselected.
            $this->categories[0] = resolve(CategoryService::class)->list(conditions: [
                'whereNull' => ['parent_id'],
            ]);
        } else {
            for ($i = $this->categoryDepth - 2; $i >= 0; $i--) {
                $this->categories[$i] = resolve(CategoryService::class)->list(conditions: [
                    'where' => ['parent_id' => ['=', $currnetParent->parent_id]],
                ]);
                $this->parents[$i] = $currnetParent->id;
                if ($currnetParent->parent_id == null) {
                    break;
                }
                $currnetParent = $currnetParent->parent;
            }

            if ($this->categoryDepth - 1 - $i != $this->categoryDepth - 1) {
                $diff = $this->categoryDepth - 1 - $i;
                foreach ($this->categories as $key => $temp) {
                    if ($key >= $diff) {
                        $this->categories[$key - $diff] = $this->categories[$key];
                        $this->categories[$key] = null;
                        $this->parents[$key - $diff] = $this->parents[$key];
                        $this->parents[$key] = null;
                    }
                }
            }
        }
        // dd($this->categories);
    }

    public function updated($name, $value)
    {
        if (str_starts_with($name, 'parents.')) {

            $level = (int) str_replace('parents.', '', $name);

            $this->categories[$level + 1] = resolve(CategoryService::class)->list(conditions: [
                'where' => ['parent_id' => ['=', $value]],
            ]);

            unset($this->parents[$level + 1]);
        }
    }

    public function update(CategoryService $categoryService)
    {
        $parentId = end($this->parents);
        $this->form['parent_id'] = $parentId ?: null;
        try {
            $this->validate(
                StoreCategoryRules::rules(),
                trans('product::validation'),
                trans('product::attributes')
            );

            $categoryService->update($this->category, $this->form);
            $this->message = __('product::messages.edit.success');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function updatedFormImage()
    {
        $this->validate(
            [
                'form.image' => ['image', 'max:'.config('media.validations.image.max'), 'mimes:'.config('media.validations.image.mimes')],
            ],
            trans('warehouse::validation'),
            trans('warehouse::attributes')
        );
    }

    public function removeImage()
    {
        $this->form['image'] = null;
    }

    public function getImagePreviewProperty()
    {
        if ($this->form['image'] instanceof TemporaryUploadedFile) {
            return $this->form['image']->temporaryUrl();
        }

        return $this->initialImage;
    }

    public function getClientOriginalNameProperty()
    {
        return $this->form['image'] instanceof TemporaryUploadedFile ?
            $this->form['image']->getClientOriginalName() :
            basename(parse_url($this->initialImage, PHP_URL_PATH));
    }

    public function render()
    {
        $types = CategoryType::cases();

        return $this->renderView(
            'Category::livewire.category.category-edit',
            compact('types')
        )->layoutData([
            'title' => __('product::attributes.category_edit'),
        ]);
    }
}
