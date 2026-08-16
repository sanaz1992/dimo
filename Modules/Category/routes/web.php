<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Livewire\Admin\Category\CategoryCreate;
use Modules\Category\Http\Livewire\Admin\Category\CategoryEdit;
use Modules\Category\Http\Livewire\Admin\Category\CategoryList;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {
        Route::get('/product-categories', CategoryList::class)
            ->middleware(['can:product_categories_list'])->name('product.categories.index');
        Route::get('/product-categories/create', CategoryCreate::class)
            ->middleware(['can:product_categories_create'])->name('product.categories.create');
        Route::get('/product-categories/{category}/edit', CategoryEdit::class)
            ->middleware(['can:product_categories_edit'])->name('product.categories.edit');
    });
