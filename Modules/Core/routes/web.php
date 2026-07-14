<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Livewire\Admin\SettingEdit;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {

        Route::get('/settings', SettingEdit::class)->middleware(['can:settings_edit'])->name('settings.edit');
    });
