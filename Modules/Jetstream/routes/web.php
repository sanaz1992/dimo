<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Modules\Jetstream\Http\Controllers\JetstreamController;

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware(['guest'])
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest']);

Route::post('/login/send-code', [JetstreamController::class, 'loginSendCode'])
    ->middleware(['guest'])->name('login.send.code');

Route::get('/login/send-code', [JetstreamController::class, 'showCheckCode'])
    ->middleware(['guest'])->name('login.check.code.form');

Route::post('/login/check-code', [JetstreamController::class, 'loginCheckCode'])
    ->middleware(['guest'])->name('login.check.code');

Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout.get');

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware(['guest'])
    ->name('register.form');

// Route::get('/register', function () {
//     return redirect()->route('login');
// })->middleware(['guest'])
//     ->name('register');

Route::post('/register', [JetstreamController::class, 'sendCode'])
    ->middleware(['guest'])
    ->name('register.send_code');

Route::post('/register/check', [JetstreamController::class, 'register'])
    ->middleware(['guest'])
    ->name('register');
