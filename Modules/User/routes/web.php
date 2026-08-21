<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Livewire\Admin\Admin\AdminCreate;
use Modules\User\Http\Livewire\Admin\Admin\AdminEdit;
use Modules\User\Http\Livewire\Admin\Admin\AdminList;
use Modules\User\Http\Livewire\Admin\Tenant\TenantCreate;
use Modules\User\Http\Livewire\Admin\Tenant\TenantEdit;
use Modules\User\Http\Livewire\Admin\Tenant\TenantList;
use Modules\User\Http\Livewire\Admin\User\UserCreate;
use Modules\User\Http\Livewire\Admin\User\UserEdit;
use Modules\User\Http\Livewire\Admin\User\UserList;

Route::name('admin.')->prefix('/admin')
    ->middleware(['auth', 'verified', 'admin.panel'])
    ->group(function () {

        Route::get('/admins/{type?}', AdminList::class)->middleware(['can:admins_list'])->name('admins.index');
        Route::get('/admins/create', AdminCreate::class)->middleware(['can:admins_create'])->name('admins.create');
        Route::get('/admins/{user}/edit', AdminEdit::class)->middleware(['can:admins_edit'])->name('admins.edit');

        Route::get('/users', UserList::class)->middleware(['can:users_list'])->name('users.index');
        Route::get('/users/create', UserCreate::class)->middleware(['can:users_create'])->name('users.create');
        Route::get('/users/{user}/edit', UserEdit::class)->middleware(['can:users_edit'])->name('users.edit');

        Route::get('/tenants', TenantList::class)->middleware(['can:tenants_list'])->name('tenants.index');
        Route::get('/tenants/create', TenantCreate::class)->middleware(['can:tenants_create'])->name('tenants.create');
        Route::get('/tenants/{tenant}/edit', TenantEdit::class)->middleware(['can:tenants_edit'])->name('tenants.edit');

    });

Route::middleware(['auth', 'verified', 'seller.panel'])->group(function () {
    // Route::get('/seller/profile', SellerProfile::class)->name('seller.profile');
});
