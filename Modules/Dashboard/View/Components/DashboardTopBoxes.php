<?php

namespace Modules\Dashboard\View\Components;

use Illuminate\View\Component;

class DashboardTopBoxes extends Component
{
    public string $panel; // admin or seller

    public function __construct(string $panel = 'admin')
    {
        $this->panel = $panel;
    }

    public function render()
    {
        return view('dashboard::components.dashboard-top-boxes');
    }
}
