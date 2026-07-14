<?php

namespace Modules\Product\Http\Livewire\Component;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\Category\Enums\CategoryType;
use Modules\Category\Services\CategoryService;

class ProductAdvancedFilters extends Component
{
    public $search = '';

    public $intermediate;

    public $category = '';

    public $price_min = '';

    public $price_max = '';

    public $published = '';

    public $isOpen = false;

    public $categories;

    public function mount(
        $intermediate = false,
        $search = null,
        $category = null,
        $price_min = null,
        $price_max = null,
        $published = null,
    ) {
        $this->intermediate = $intermediate;
        $this->search = $search;
        $this->category = $category;
        $this->price_min = $price_min;
        $this->price_max = $price_max;
        $this->published = $published;

        $this->categories = resolve(CategoryService::class)->list(
            orderBy: 'title',
            conditions: [
                'where' => ['type' => ['=', CategoryType::PRODUCT->value]],
            ]
        );
    }

    #[Computed]
    public function activeFiltersCount()
    {
        $count = 0;
        if ($this->search) {
            $count++;
        }

        if ($this->category) {
            $count++;
        }
        if ($this->price_min || $this->price_max) {
            $count++;
        }
        if ($this->published != '') {
            $count++;
        }

        return $count;
    }

    public function toggleModal()
    {
        // $this->dispatch('reinit-datepickers');
        $this->isOpen = ! $this->isOpen;
    }

    public function apply()
    {
        $this->dispatch('updateProductListFilters', [
            'search' => $this->search,
            'category' => $this->category,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'published' => $this->published,
        ]);

        $this->isOpen = false;
    }

    public function clearAll()
    {
        $this->reset([
            'search',
            'category',
            'price_min',
            'price_max',
            'published',
        ]);
        $this->apply();
    }

    public function render()
    {
        return view('Product::components.product-filters');
    }
}
