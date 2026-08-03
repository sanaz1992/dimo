<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\Http\Livewire\Admin\TransactionList;
use Modules\Transactions\Http\Livewire\Admin\TransactionShow;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {

        Route::get('/transactions', TransactionList::class)->middleware(['can:transactions_list'])->name('transactions.index');
        Route::get('/transactions/{transaction}/show', TransactionShow::class)->middleware(['can:transactions_show'])->name('transactions.show');

    });
