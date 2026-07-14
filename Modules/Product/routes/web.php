<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Livewire\CostItem\CostItemList;
use Modules\Product\Http\Livewire\Product\ProductCreate;
use Modules\Product\Http\Livewire\Product\ProductEdit;
use Modules\Product\Http\Livewire\Product\ProductImport;
use Modules\Product\Http\Livewire\Product\ProductList;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {
        Route::get('/products', ProductList::class)
            ->middleware(['can:products_list'])->name('products.index');
        Route::get('/products/import', ProductImport::class)->name('products.import');
        Route::get('/products/create', ProductCreate::class)
            ->middleware(['can:products_create'])->name('products.create');
        Route::get('/products/{product}/edit', ProductEdit::class)
            ->middleware(['can:products_edit'])->name('products.edit');

        Route::get('/cost-items', CostItemList::class)->middleware(['can:cost_items_list'])->name('cost.items.index');
    });
