<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Livewire\Admin\PurchaseCreate;
use Modules\Inventory\Http\Livewire\Admin\PurchaseEdit;
use Modules\Inventory\Http\Livewire\Admin\PurchaseList;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {

        Route::get('/purchases', PurchaseList::class)->middleware(['can:purchases_list'])->name('purchases.index');
        Route::get('/purchases/create', PurchaseCreate::class)
            ->middleware(['can:purchases_create'])->name('purchases.create');
        Route::get('/purchases/{purchase}/edit', PurchaseEdit::class)
            ->middleware(['can:purchases_edit'])->name('purchases.edit');

    });
