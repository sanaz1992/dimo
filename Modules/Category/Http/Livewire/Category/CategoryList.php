<?php

namespace Modules\Category\Http\Livewire\Category;

use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Category\Enums\CategoryType;
use Modules\Category\Services\CategoryService;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Product\Services\ProductService;
use Modules\Warehouse\Services\RawMaterialWarehouseService;

class CategoryList extends AdminBaseComponent
{
    use LivewireNotify;
    use WithPagination;

    public $showDeleteModal = [];

    public $errorMessage = '';

    public string $activeTab = '';

    public array $allTabs = [];

    protected $queryString = ['activeTab' => ['except' => '']];

    public function mount()
    {
        $this->allTabs = CategoryType::labels();

        if (empty($this->activeTab) || ! array_key_exists($this->activeTab, $this->allTabs)) {
            $this->activeTab = array_key_first($this->allTabs);
        }

        $categories = resolve(CategoryService::class)->list('id:asc', [], ['mainImageRelation']);
        foreach ($categories as $category) {
            $this->showDeleteModal[$category->id] = false;
        }
        // $this->authorize('categories_list');
    }

    #[On('tabChanged')]
    public function setActive(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    /**
     * Flatten all categories into a parent-first ordered list (depth-first)
     * so the listing can be rendered as a tree. Each node is annotated with a
     * `treeDepth` attribute (0 = root) used for indentation in the view.
     */
    protected function buildTree($categories)
    {
        $byParent = [];
        foreach ($categories as $category) {
            $byParent[$category->parent_id ?? 0][] = $category;
        }

        $ordered = collect();
        $walk = function ($parentKey, int $depth) use (&$walk, &$byParent, $ordered) {
            foreach ($byParent[$parentKey] ?? [] as $node) {
                $node->treeDepth = $depth;
                $ordered->push($node);
                $walk($node->id, $depth + 1);
            }
        };
        $walk(0, 0);

        return $ordered;
    }

    public function deleteCategory(int $categoryId): void
    {
        $categoryService = resolve(CategoryService::class);
        $category = $categoryService->find($categoryId);

        $subCategories = $categoryService->getSubCategories($category);
        if ($category->type === CategoryType::RAW_MATERIAL) {
            $categorizables = resolve(RawMaterialWarehouseService::class)->list(
                conditions: [
                    'where' => ['category_id' => ['=', $category->id]],
                ]
            );
        } else {
            $categorizables = resolve(ProductService::class)->list(
                conditions: [
                    'where' => ['category_id' => ['=', $category->id]],
                ]
            );
        }

        $subCategoriesCount = $subCategories->count();
        $categorizablesCount = $categorizables->count();
        if ($subCategoriesCount > 0 || $categorizablesCount > 0) {
            $this->errorMessage = __('category::messages.delete_error_message', ['item_count' => $categorizablesCount, 'subcategory_count' => $subCategoriesCount]);
            $this->showDeleteModal[$categoryId] = true;
        } else {
            resolve(CategoryService::class)->delete($category);
            $this->notify('success', __('category::messages.category_deleted'));
        }
    }

    public function confirmDelete($id, $replacementCategoryId)
    {
        if (! $replacementCategoryId) {
            return $this->notify('error', __('category::messages.category_not_found'));
        }
        $category = resolve(CategoryService::class)->find($id);
        $replacementCategory = resolve(CategoryService::class)->find($replacementCategoryId);

        if ($replacementCategory->type !== $category->type) {
            return $this->notify('error', __('category::messages.category_types_dont_match'));
        }
        if ($category->type === CategoryType::RAW_MATERIAL) {
            $category->rawMaterials()->update([
                'category_id' => $replacementCategory->id,
            ]);
        } elseif ($category->type === CategoryType::PRODUCT) {
            $category->products()->update([
                'category_id' => $replacementCategory->id,
            ]);
        }
        $category->subCategories()->update(['parent_id' => $replacementCategory->id]);

        $deleted = resolve(CategoryService::class)->deleteById($id);

        if ($deleted) {
            $this->notify('success', __('category::messages.category_deleted'));
        } else {
            $this->notify('error', __('category::messages.category_not_deleted'));
        }
        $this->showDeleteModal[$category->id] = false;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
    }

    public function render(CategoryService $categoryService)
    {
        $allCategories = $categoryService->list('id:asc', [], ['mainImageRelation'], [
            'where' => ['type' => ['=', $this->activeTab]],
        ]);
        $categories = $this->buildTree($allCategories);

        return $this->renderView(
            'Category::livewire.category.category-list',
            compact('categories')
        )->layoutData([
            'title' => __('product::attributes.category_list'),
        ]);
    }
}
