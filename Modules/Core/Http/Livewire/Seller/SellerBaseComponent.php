<?php

namespace Modules\Core\Http\Livewire\Seller;

use Livewire\Component;

class SellerBaseComponent extends Component
{
    protected function renderView($view, $data = [])
    {
        return view($view, $data)->layout('Core::layouts.seller');
    }
}
