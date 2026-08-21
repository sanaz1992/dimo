<?php

use Illuminate\Support\Facades\Route;
use Modules\Instagram\Http\Controllers\InstagramAuthController;
use Modules\Instagram\Http\Livewire\Admin\InstagramAccount\InstagramAccountList;

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

Route::name('admin.')->prefix('/admin')
    ->middleware(['auth', 'verified', 'admin.panel'])
    ->group(function () {

        Route::get('/instagram-accounts', InstagramAccountList::class)
            ->middleware(['can:instagram_accounts_list'])->name('instagram_accounts.index');
    });

Route::name('user.')->prefix('/user')
    ->middleware(['auth'])
    ->group(function () {

        Route::get('/instagram-accounts', InstagramAccountList::class)
            ->middleware(['can:instagram_accounts_list'])->name('instagram_accounts.index');
    });
