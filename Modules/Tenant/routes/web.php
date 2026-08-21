<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Livewire\Admin\Tenant\TenantCreate;
use Modules\Tenant\Http\Livewire\Admin\Tenant\TenantEdit;
use Modules\Tenant\Http\Livewire\Admin\Tenant\TenantList;
use Modules\Tenant\Http\Livewire\User\Tenant\UserTenantCreate;
use Modules\Tenant\Http\Livewire\User\Tenant\UserTenantEdit;
use Modules\Tenant\Http\Livewire\User\Tenant\UserTenantList;

Route::name('admin.')->prefix('/admin')
    ->middleware(['auth', 'verified', 'admin.panel'])
    ->group(function () {

        Route::get('/tenants', TenantList::class)->middleware(['can:tenants_list'])->name('tenants.index');
        Route::get('/tenants/create', TenantCreate::class)->middleware(['can:tenants_create'])->name('tenants.create');
        Route::get('/tenants/{tenant}/edit', TenantEdit::class)->middleware(['can:tenants_edit'])->name('tenants.edit');
    });

Route::name('user.')->prefix('/user')
    ->middleware(['auth'])
    ->group(function () {

        Route::get('/tenants', UserTenantList::class)->name('tenants.index');
        Route::get('/tenants/create', UserTenantCreate::class)->name('tenants.create');
        Route::get('/tenants/{tenant}/edit', UserTenantEdit::class)->name('tenants.edit');
    });
