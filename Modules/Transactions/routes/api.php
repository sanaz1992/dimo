<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\Http\Controllers\TransactionsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('transactions', TransactionsController::class)->names('transactions');
});
