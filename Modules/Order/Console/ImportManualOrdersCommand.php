<?php

namespace Modules\Order\Console;

use Illuminate\Console\Command;
use Modules\Order\Services\ManualOrderImportService;
use Modules\User\Entities\User;

class ImportManualOrdersCommand extends Command
{
    protected $signature = 'order:import-manual
        {file : Path to the .xlsx file}
        {--seller-mobile=09358364707 : Mobile of the admin who will own the imported orders}
        {--fresh : Delete previously imported MANUAL orders for this seller before importing (re-import)}
        {--commit : Persist changes (without this flag it is a dry-run)}';

    protected $description = 'Import a historical invoice list (xlsx) as MANUAL orders with pending pricing';

    public function handle(): int
    {
        $file = $this->argument('file');
        if (! is_file($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $seller = User::where('mobile', $this->option('seller-mobile'))->first();
        if (! $seller) {
            $this->error('Seller (admin) not found for mobile: '.$this->option('seller-mobile'));

            return self::FAILURE;
        }

        $dryRun = ! $this->option('commit');
        $fresh = (bool) $this->option('fresh');
        $this->info(($dryRun ? '[DRY-RUN] ' : '[COMMIT] ').($fresh ? '[FRESH] ' : '')."Importing {$file} — owner: {$seller->name} (#{$seller->id})");

        $report = (new ManualOrderImportService($seller->id))->import($file, $dryRun, $fresh);

        $this->newLine();
        $this->line('Deleted (re-import) .... '.$report['deleted_orders']);
        $this->line('Orders created ......... '.$report['orders_created']);
        $this->line('Order items created .... '.$report['items_created']);
        $this->line('Fabric items created ... '.$report['fabric_items_created']);
        $this->line('Stub products created .. '.count($report['stub_products_created']));
        $this->line('Rows skipped ........... '.$report['skipped_rows']);
        $this->line('Orders skipped ......... '.count($report['skipped_orders']));
        $this->line('Duplicate invoices ..... '.count($report['duplicate_orders']));
        $this->line('Unmapped frame colors .. '.count($report['unmapped_colors']));
        $this->line('Matched sellers ........ '.count($report['matched_sellers']));
        $this->line('Unmatched نماینده ...... '.count($report['unmatched_persons']));

        $this->section('Stub products created', $report['stub_products_created']);
        $this->section('Orders skipped', $report['skipped_orders']);
        $this->section('Unmapped frame colors (frame_color_id left null)', $report['unmapped_colors']);
        $this->section('Matched sellers (نماینده)', array_values(array_unique($report['matched_sellers'])));
        $this->section('Unmatched نماینده (fell back to admin owner)', $report['unmatched_persons']);

        $this->newLine();
        if ($dryRun) {
            $this->warn('Dry-run only — nothing was saved. Re-run with --commit to persist.');
        } else {
            $this->info('Done. Changes committed.');
        }

        return self::SUCCESS;
    }

    protected function section(string $title, array $items): void
    {
        if (empty($items)) {
            return;
        }
        $this->newLine();
        $this->comment($title.':');
        foreach ($items as $item) {
            $this->line('  - '.$item);
        }
    }
}
