<?php

namespace Modules\Order\Http\Livewire\Component;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\Factory\Entities\Hall;

class AdvancedFilters extends Component
{
    public $code = '';

    public $seller = '';

    public $created_at_from = '';

    public $created_at_to = '';

    public $started_at_from = '';

    public $started_at_to = '';

    public $finished_at_from = '';

    public $finished_at_to = '';

    public $price_min = '';

    public $price_max = '';

    public $hall = '';

    public function mount(
        $code = null,
        $seller = null,
        $created_at_from = null,
        $created_at_to = null,
        $started_at_from = null,
        $started_at_to = null,
        $finished_at_from = null,
        $finished_at_to = null,
        $price_min = null,
        $price_max = null,
        $hall = null
    ) {
        $this->code = $code;
        $this->seller = $seller;
        $this->created_at_from = $created_at_from;
        $this->created_at_to = $created_at_to;
        $this->started_at_from = $started_at_from;
        $this->started_at_to = $started_at_to;
        $this->finished_at_from = $finished_at_from;
        $this->finished_at_to = $finished_at_to;
        $this->price_min = $price_min;
        $this->price_max = $price_max;
        $this->hall = $hall;
    }

    #[Computed]
    public function activeFiltersCount()
    {
        $count = 0;

        if ($this->code) {
            $count++;
        }
        if ($this->seller) {
            $count++;
        }
        if ($this->created_at_from) {
            $count++;
        }
        if ($this->created_at_to) {
            $count++;
        }
        if ($this->started_at_from) {
            $count++;
        }
        if ($this->started_at_to) {
            $count++;
        }
        if ($this->finished_at_from) {
            $count++;
        }
        if ($this->finished_at_to) {
            $count++;
        }
        if ($this->price_min) {
            $count++;
        }
        if ($this->price_max) {
            $count++;
        }
        if ($this->hall) {
            $count++;
        }

        return $count;
    }

    public function apply()
    {
        $this->dispatch('updateOrderListFilters', [
            'code' => $this->code,
            'seller' => $this->seller,
            'created_at_from' => $this->created_at_from,
            'created_at_to' => $this->created_at_to,
            'started_at_from' => $this->started_at_from,
            'started_at_to' => $this->started_at_to,
            'finished_at_from' => $this->finished_at_from,
            'finished_at_to' => $this->finished_at_to,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'hall' => $this->hall,
        ]);
    }

    public function clearAll()
    {
        $this->reset(['code', 'seller', 'created_at_from', 'created_at_to', 'started_at_from', 'started_at_to', 'finished_at_from', 'finished_at_to', 'price_min', 'price_max', 'hall']);
        $this->apply();
    }

    public function render()
    {
        $halls = Hall::all();

        return view('Order::components.filters', compact('halls'));
    }
}
