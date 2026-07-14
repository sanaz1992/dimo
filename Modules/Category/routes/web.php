<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Livewire\Category\CategoryCreate;
use Modules\Category\Http\Livewire\Category\CategoryEdit;
use Modules\Category\Http\Livewire\Category\CategoryList;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {

        Route::get('/categories', CategoryList::class)
            ->middleware(['can:categories_list'])->name('categories.index');
        Route::get('/categories/create', CategoryCreate::class)
            ->middleware(['can:categories_create'])->name('categories.create');
        Route::get('/categories/{category}/edit', CategoryEdit::class)
            ->middleware(['can:categories_edit'])->name('categories.edit');
    });
