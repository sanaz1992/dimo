<?php

use Illuminate\Support\Facades\Route;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Instagram\Http\Controllers\InstagramAuthController;
use Modules\Instagram\Http\Livewire\Admin\InstagramAccount\InstagramAccountList;
use Modules\Instagram\Http\Livewire\User\InstagramAccount\UserInstagramAccountList;
use Modules\Instagram\Services\InstagramMessageService;

Route::middleware(['auth'])->group(function () {
    // Route::get(
    //     '/instagram/connect',
    //     [InstagramAuthController::class, 'redirect']
    // )->name('instagram.connect');
});

Route::get(
    '/auth/instagram/callback',
    [InstagramAuthController::class, 'callback']
)->name('instagram.callback');

Route::name('admin.')->prefix('/admin')
    ->middleware(['auth', 'verified', 'admin.panel'])
    ->group(function () {

        Route::get('/instagram/connect', [InstagramAuthController::class, 'redirect'])->name('instagram.connect');

        Route::get('/instagram-accounts', InstagramAccountList::class)
            ->middleware(['can:instagram_accounts_list'])->name('instagram_accounts.index');
    });

Route::name('user.')->prefix('/user')
    ->middleware(['auth'])
    ->group(function () {

        Route::get('/instagram/connect', [InstagramAuthController::class, 'redirect'])->name('instagram.connect');

        Route::get('/instagram-accounts', UserInstagramAccountList::class)->name('instagram_accounts.index');
    });

Route::get('/instagram/test-send', function (InstagramMessageService $messageService) {
    $account = InstagramAccount::find(4);

    if (! $account) {
        return response()->json([
            'error' => 'Instagram account not found',
        ], 404);
    }

    $result = $messageService->sendTextMessage(
        instagramAccount: $account,
        recipientIgId: '891737287065342',
        message: 'سلام، این یک پیام تستی از سیستم است 👋',
    );

    return response()->json($result);
});
