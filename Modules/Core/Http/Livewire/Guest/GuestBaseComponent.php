<?php

namespace Modules\Core\Http\Livewire\Guest;

use Livewire\Component;

class GuestBaseComponent extends Component
{
    protected function renderView($view, $data = [])
    {
        return view($view, $data)->layout('Core::layouts.guest');
    }
}
