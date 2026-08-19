<?php

use Illuminate\Support\Facades\Route;
use Modules\Instagram\Http\Controllers\InstagramController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('instagrams', InstagramController::class)->names('instagram');
});
