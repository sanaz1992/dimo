<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Livewire\Admin\AdminDashboard;
use Modules\Dashboard\Http\Livewire\User\UserDashboard;

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('dashboard');
});

Route::prefix('admin')
    ->middleware(['auth', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    });

Route::prefix('user')
    ->middleware(['auth'])
    ->name('user.')
    ->prefix('/user')
    ->group(function () {
        Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    });
