<?php

namespace Modules\UI\Http\Livewire;

use Livewire\Component;

class Tabs extends Component
{
    public $tabs = [];

    public $active;

    public $wrapperClass;

    public function mount($tabs = [], $active = null, $wrapperClass = null)
    {
        $this->wrapperClass = $wrapperClass;
        $this->tabs = $tabs;
        $this->active = $active ?? (count($tabs) ? array_key_first($tabs) : null);
    }

    public function setActive($key)
    {
        $this->active = $key;
        $this->dispatch('tabChanged', $key);  // set event for parent component
    }

    public function render()
    {
        return view('UI::livewire.tabs');
    }
}
