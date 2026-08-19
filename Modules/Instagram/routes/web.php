<?php

use Illuminate\Support\Facades\Route;
use Modules\Instagram\Http\Controllers\InstagramController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('instagrams', InstagramController::class)->names('instagram');
});
