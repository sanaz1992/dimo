<?php

namespace Modules\Shop\Http\Livewire;

use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;

class HomePage extends GuestBaseComponent
{
    public function mount() {}

    public function render()
    {
        return $this->renderView(
            'Shop::livewire.guest.home'
        )->layoutData([
            'title' => __('shop::attributes.home_page'),
        ]);
    }
}
