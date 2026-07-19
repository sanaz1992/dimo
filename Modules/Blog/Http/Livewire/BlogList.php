<?php

namespace Modules\Blog\Http\Livewire;

use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;

class BlogList extends GuestBaseComponent
{
    public function mount() {}

    public function render()
    {
        return $this->renderView(
            'Blog::livewire.guest.home'
        )->layoutData([
            'title' => __('blog::attributes.home_page'),
        ]);
    }
}
