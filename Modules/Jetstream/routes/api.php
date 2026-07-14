<?php

use Illuminate\Support\Facades\Route;
use Modules\Jetstream\Http\Controllers\JetstreamController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('jetstreams', JetstreamController::class)->names('jetstream');
});
