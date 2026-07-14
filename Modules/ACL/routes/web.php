<?php

use Illuminate\Support\Facades\Route;
use Modules\ACL\Http\Livewire\RoleCreate;
use Modules\ACL\Http\Livewire\RoleEdit;
use Modules\ACL\Http\Livewire\RoleList;
use Modules\ACL\Http\Livewire\UserRolesEdit;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {

        Route::get('/roles', RoleList::class)->middleware(['can:roles_list'])->name('roles.index');
        Route::get('/roles/create', RoleCreate::class)->middleware(['can:roles_create'])->name('roles.create');
        Route::get('/roles/{role}/edit', RoleEdit::class)->middleware(['can:roles_edit'])->name('roles.edit');

        Route::get('/users/{user}/roles', UserRolesEdit::class)->middleware(['can:admins_roles_edit'])->name('users.roles.edit');
    });
