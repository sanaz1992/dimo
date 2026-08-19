<?php

use Illuminate\Support\Facades\Route;
use Modules\Instagram\Http\Controllers\InstagramAuthController;

Route::middleware(['auth'])->group(function () {

    Route::get(
        '/instagram/connect',
        [InstagramAuthController::class, 'redirect']
    )->name('instagram.connect');

});

Route::get(
    '/auth/instagram/callback',
    [InstagramAuthController::class, 'callback']
)->name('instagram.callback');
