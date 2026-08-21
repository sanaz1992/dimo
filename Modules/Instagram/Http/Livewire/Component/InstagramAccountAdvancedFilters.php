<?php

namespace Modules\Instagram\Http\Livewire\Component;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\Core\Enums\LocalEnum;
use Modules\Core\Enums\TimeZoneEnum;
use Modules\Tenant\Enums\TenantStatus;

class InstagramAccountAdvancedFilters extends Component
{
    public $isOpen = false;

    public $filterData = [
        'search' => '',
        'timezone' => '',
        'local' => '',
        'status' => '',
    ];

    public function mount(
        $filterData = [],
    ) {
        $this->filterData = array_merge(
            [
                'search' => '',
                'timezone' => '',
                'local' => '',
                'status' => '',
            ],
            $filterData
        );
    }

    #[Computed]
    public function activeFiltersCount()
    {
        return collect($this->filterData)
            ->filter(fn ($value) => filled($value))
            ->count();
    }

    public function toggleModal()
    {
        // $this->dispatch('reinit-datepickers');
        $this->isOpen = ! $this->isOpen;
    }

    public function apply()
    {
        $this->dispatch(
            'updateTenantListFilters',
            filterData: $this->filterData
        );

        $this->isOpen = false;
    }

    public function clearAll()
    {
        $this->filterData = [
            'search' => '',
            'timezone' => '',
            'local' => '',
            'status' => '',
        ];

        $this->dispatch(
            'updateTenantListFilters',
            filterData: $this->filterData
        );

        $this->isOpen = false;
    }

    public function render()
    {
        $timezones = TimeZoneEnum::labels();
        $locals = LocalEnum::labels();
        $tenantStatuses = TenantStatus::labels();

        return view('Tenant::components.tenant-filters', compact('timezones', 'locals', 'tenantStatuses'));
    }
}
