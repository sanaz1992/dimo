<?php

namespace Modules\Core\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Product\Services\ProductService;

class SearchNavbar extends Component
{
    public $showSearchBox = false;

    public $products;

    public $search;

    public function changeQuery($value)
    {
        $this->search = $value;
        if ($value) {
            $this->showSearchBox = true;
            $conditions = [
                'where' => [
                    'title' => ['LIKE', $value],
                ],
                'orWhere' => [
                    'code' => ['LIKE', $value],
                ],
            ];
            $this->products = resolve(ProductService::class)
                ->list(null, [], [], $conditions);
        } else {
            $this->products = [];
            $this->showSearchBox = false;
        }
    }

    public function render()
    {
        return view('Core::livewire.admin.search-navbar');
    }
}
