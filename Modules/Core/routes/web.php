<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Livewire\Admin\AdminTagList;
use Modules\Core\Http\Livewire\Admin\SettingEdit;
use Modules\Core\Http\Livewire\User\UserTagList;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {

        Route::get('/settings', SettingEdit::class)->middleware(['can:settings_edit'])->name('settings.edit');

        Route::get('/tags', AdminTagList::class)->middleware(['can:tags_list'])->name('tags.index');
    });

Route::middleware(['auth', 'verified'])
    ->name('user.')
    ->prefix('/user')
    ->group(function () {

        Route::get('/tags', UserTagList::class)->name('tags.index');
    });
