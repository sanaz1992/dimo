<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Livewire\Admin\Product\ProductCreate;
use Modules\Product\Http\Livewire\Admin\Product\ProductEdit;
use Modules\Product\Http\Livewire\Admin\Product\ProductImport;
use Modules\Product\Http\Livewire\Admin\Product\ProductList;

use Modules\Product\Http\Livewire\Guest\Product\ProductList as GuestProductList;

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
    });

Route::get('/products', GuestProductList::class)->name('products.index');
