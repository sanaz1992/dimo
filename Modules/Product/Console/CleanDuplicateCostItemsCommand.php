<?php

namespace Modules\Product\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDuplicateCostItemsCommand extends Command
{
    protected $signature = 'product:clean-duplicate-cost-items';

    protected $description = 'Remove duplicate cost item rows from product_cost_items pivot table, keeping one row per product-cost_item pair';

    public function handle(): int
    {
        $duplicates = DB::table('product_cost_items')
            ->select('product_id', 'cost_item_id', DB::raw('COUNT(*) as cnt'), DB::raw('MIN(id) as keep_id'))
            ->groupBy('product_id', 'cost_item_id')
            ->having('cnt', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate cost items found. Everything is clean.');

            return self::SUCCESS;
        }

        $totalDeleted = 0;

        foreach ($duplicates as $dup) {
            $deleted = DB::table('product_cost_items')
                ->where('product_id', $dup->product_id)
                ->where('cost_item_id', $dup->cost_item_id)
                ->where('id', '!=', $dup->keep_id)
                ->delete();

            $totalDeleted += $deleted;

            $this->line("  Product #{$dup->product_id} / CostItem #{$dup->cost_item_id}: removed {$deleted} duplicate(s)");
        }

        $this->info("Done. Removed {$totalDeleted} duplicate row(s) across {$duplicates->count()} product-cost_item pair(s).");

        return self::SUCCESS;
    }
}
