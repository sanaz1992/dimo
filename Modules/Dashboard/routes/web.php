<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Livewire\Admin\AdminDashboard;
use Modules\Dashboard\Http\Livewire\Seller\SellerDashboard;

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

Route::prefix('seller')
    ->middleware(['auth', 'seller.panel'])
    ->name('seller.')
    ->prefix('/seller')
    ->group(function () {
        Route::get('/dashboard', SellerDashboard::class)->name('dashboard');
    });
