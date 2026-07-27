<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\Http\Controllers\PaymentCallbackController;

Route::prefix('transactions')->name('transactions.')->group(function () {
    Route::get('/callback/{order}', PaymentCallbackController::class)
        ->name('callback');
});
