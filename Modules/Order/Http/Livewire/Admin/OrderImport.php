<?php

namespace Modules\Order\Http\Livewire\Admin;

use Livewire\WithFileUploads;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Services\ManualOrderImportService;

class OrderImport extends AdminBaseComponent
{
    use LivewireNotify;
    use WithFileUploads;

    public $file;

    /** Delete previously imported MANUAL orders for this seller before importing. */
    public bool $fresh = false;

    /** Dry-run report shown for confirmation before committing. */
    public ?array $report = null;

    /** True once a real import has been committed. */
    public bool $imported = false;

    protected array $rules = [
        'file' => 'required|file|mimes:xlsx,xls',
    ];

    /** Step 1: run a dry-run and show the preview without touching the database. */
    public function preview()
    {
        $this->validate();
        $this->imported = false;

        try {
            $this->report = $this->service()->import($this->file->getRealPath(), true, $this->fresh);
        } catch (\Throwable $e) {
            report($e);
            $this->notify('error', __('order::messages.import_failed'));
        }
    }

    /** Step 2: user confirmed the preview — persist the import. */
    public function confirmImport()
    {
        $this->validate();

        try {
            $this->report = $this->service()->import($this->file->getRealPath(), false, $this->fresh);
            $this->imported = true;
            $this->notify('success', __('order::messages.import_success'));
        } catch (\Throwable $e) {
            report($e);
            $this->notify('error', __('order::messages.import_failed'));
        }
    }

    /** Discard the current preview and start over. */
    public function resetImport()
    {
        $this->reset(['file', 'report', 'imported', 'fresh']);
    }

    protected function service(): ManualOrderImportService
    {
        return new ManualOrderImportService(auth()->id());
    }

    public function render()
    {
        return $this->renderView('Order::livewire.order.admin.order-import')
            ->layoutData(['title' => __('order::attributes.import_orders')]);
    }
}
