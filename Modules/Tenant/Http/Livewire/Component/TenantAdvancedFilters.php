<?php

namespace Modules\Tenant\Http\Livewire\Component;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\Core\Enums\LocalEnum;
use Modules\Core\Enums\TimeZoneEnum;
use Modules\Tenant\Enums\TenantStatus;

class TenantAdvancedFilters extends Component
{
    public $showFilterModal = false;

    public $filterData = [
        'name' => '',
        'timezone' => '',
        'local' => '',
        'status' => '',
    ];

    public function mount(
        $filterData = [],
    ) {
        $this->filterData = array_merge(
            [
                'name' => '',
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

    public function openFilterModal(): void
    {
        $this->showFilterModal = true;
    }

    public function closeFilterModal(): void
    {
        $this->showFilterModal = false;
    }

    public function apply()
    {
        $this->dispatch(
            'updateTenantListFilters',
            filterData: $this->filterData
        );

        $this->showFilterModal = false;
    }

    public function clearAll()
    {
        $this->filterData = [
            'name' => '',
            'timezone' => '',
            'local' => '',
            'status' => '',
        ];

        $this->dispatch(
            'updateTenantListFilters',
            filterData: $this->filterData
        );

        $this->showFilterModal = false;
    }

    public function render()
    {
        $timezones = TimeZoneEnum::labels();
        $locals = LocalEnum::labels();
        $tenantStatuses = TenantStatus::labels();

        return view('Tenant::components.tenant-filters', compact('timezones', 'locals', 'tenantStatuses'));
    }
}
