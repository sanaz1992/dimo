<?php

namespace Modules\Pages\Http\Livewire;

use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;

class About extends GuestBaseComponent
{
    public function mount() {}

    public function render()
    {
        return $this->renderView(
            'Pages::livewire.guest.home'
        )->layoutData([
            'title' => __('pages::attributes.home_page'),
        ]);
    }
}
