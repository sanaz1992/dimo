<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\Http\Controllers\TransactionsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('transactions', TransactionsController::class)->names('transactions');
});
