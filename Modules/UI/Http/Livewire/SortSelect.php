<?php

namespace Modules\UI\Http\Livewire;

use Livewire\Component;

class SortSelect extends Component
{
    public $sortBy;

    public $options = [];

    public function mount($sortBy = null, $options = [])
    {
        $this->sortBy = $sortBy;
        $this->options = $options;
    }

    public function updatedSortBy($value)
    {
        $this->dispatch('sortByChanged', $value); // set event for parent component
    }

    public function render()
    {
        return view('UI::livewire.sort-select');
    }
}
