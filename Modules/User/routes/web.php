<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Livewire\Admin\Admin\AdminCreate;
use Modules\User\Http\Livewire\Admin\Admin\AdminEdit;
use Modules\User\Http\Livewire\Admin\Admin\AdminList;
use Modules\User\Http\Livewire\Admin\User\UserCreate;
use Modules\User\Http\Livewire\Admin\User\UserEdit;
use Modules\User\Http\Livewire\Admin\User\UserList;

Route::name('admin.')->prefix('/admin')
    ->middleware(['auth', 'verified', 'admin.panel'])
    ->group(function () {

        Route::get('/admins/{type?}', AdminList::class)->middleware(['can:admins_list'])->name('admins.index');
        Route::get('/admins/{user}/show', AdminEdit::class)->middleware(['can:admins_edit'])->name('admins.show');
        Route::get('/admins/create', AdminCreate::class)->middleware(['can:admins_create'])->name('admins.create');
        Route::get('/admins/{user}/edit', AdminEdit::class)->middleware(['can:admins_edit'])->name('admins.edit');

        Route::get('/users', UserList::class)
            ->middleware(['can:users_list'])
            ->name('users.index');
        Route::get('/users/{user}/show', UserEdit::class)->middleware(['can:users_edit'])->name('users.show');
        Route::get('/users/create', UserCreate::class)->middleware(['can:users_create'])->name('users.create');
        Route::get('/users/{user}/edit', UserEdit::class)->middleware(['can:users_edit'])->name('users.edit');
    });

Route::middleware(['auth', 'verified', 'seller.panel'])->group(function () {
    // Route::get('/seller/profile', SellerProfile::class)->name('seller.profile');
});
