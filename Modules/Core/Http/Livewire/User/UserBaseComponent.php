<?php

namespace Modules\Core\Http\Livewire\User;

use Livewire\Component;

class UserBaseComponent extends Component
{
    protected function renderView($view, $data = [])
    {
        return view($view, $data)->layout('Core::layouts.user');
    }
}
