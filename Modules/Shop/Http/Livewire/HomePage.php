<?php

namespace Modules\Shop\Http\Livewire;

use Modules\Category\Services\CategoryService;
use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;

class HomePage extends GuestBaseComponent
{
    public $categories;

    public function mount() {}

    public function render()
    {
        $this->categories = resolve(CategoryService::class)->list(null, [6, false]);

        return $this->renderView(
            'Shop::livewire.guest.home'
        )->layoutData([
            'title' => __('shop::attributes.home_page'),
        ]);
    }
}
