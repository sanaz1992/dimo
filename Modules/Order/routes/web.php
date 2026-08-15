<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Livewire\Admin\OrderList;
use Modules\Order\Http\Livewire\Admin\OrderShow;
use Modules\Order\Http\Livewire\User\UserOrderList;
use Modules\Order\Http\Livewire\User\UserOrderShow;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {

        Route::get('/orders', OrderList::class)->middleware(['can:orders_list'])->name('orders.index');
        Route::get('/orders/{order}/show', OrderShow::class)->middleware(['can:orders_show'])->name('orders.show');

    });

Route::middleware(['auth', 'verified'])
    ->name('user.')
    ->prefix('/user')
    ->group(function () {

        Route::get('/orders', UserOrderList::class)->name('orders.index');
        Route::get('/orders/{order}', UserOrderShow::class)->name('orders.show');
    });
