<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\Http\Controllers\PaymentCallbackController;
use Modules\Transactions\Http\Livewire\Admin\TransactionList;
use Modules\Transactions\Http\Livewire\Admin\TransactionShow;
use Modules\Transactions\Http\Livewire\User\UserTransactionList;
use Modules\Transactions\Http\Livewire\User\UserTransactionShow;

Route::prefix('transactions')->name('transactions.')->group(function () {
    Route::get('/callback/{order}', PaymentCallbackController::class)
        ->name('callback');
});

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {

        Route::get('/transactions', TransactionList::class)->middleware(['can:transactions_list'])->name('transactions.index');
        Route::get('/transactions/{transaction}/show', TransactionShow::class)->middleware(['can:transactions_show'])->name('transactions.show');

    });

Route::middleware(['auth', 'verified'])
    ->name('user.')
    ->prefix('/user')
    ->group(function () {

        Route::get('/transactions', UserTransactionList::class)->name('transactions.index');
        Route::get('/transactions/{transaction}/show', UserTransactionShow::class)->name('transactions.show');

    });
