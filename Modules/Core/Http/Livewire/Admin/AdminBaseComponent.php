<?php

namespace Modules\Core\Http\Livewire\Admin;

use Livewire\Component;

class AdminBaseComponent extends Component
{
    protected function renderView($view, $data = [])
    {
        return view($view, $data)->layout('Core::layouts.admin');
    }
}
