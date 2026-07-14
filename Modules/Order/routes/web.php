<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Entities\Order;
use Modules\Order\Http\Controllers\OrderController;
use Modules\Order\Http\Livewire\Admin\OrderCreate;
use Modules\Order\Http\Livewire\Admin\OrderEdit;
use Modules\Order\Http\Livewire\Admin\OrderImport;
use Modules\Order\Http\Livewire\Admin\OrderList;
use Modules\Order\Http\Livewire\Admin\OrderShow;
use Modules\Order\Http\Livewire\Admin\OrderTrackingShow;
use Modules\Order\Http\Livewire\Seller\SellerOrderCreate;
use Modules\Order\Http\Livewire\Seller\SellerOrderEdit;
use Modules\Order\Http\Livewire\Seller\SellerOrderList;
use Modules\Order\Http\Livewire\Seller\SellerOrderShow;

Route::middleware(['auth', 'verified', 'admin.panel'])
    ->name('admin.')
    ->prefix('/admin')
    ->group(function () {
        Route::get('/orders/done-orders', [OrderController::class, 'doneAllOrders']);

        Route::get('/orders', OrderList::class)->middleware(['can:orders_list'])->name('orders.index');
        Route::get('/orders/import', OrderImport::class)->middleware(['can:orders_create'])->name('orders.import');
        Route::get('/orders/{order}/show', OrderShow::class)->middleware(['can:orders_show'])->name('orders.show');
        Route::get('/orders/create', OrderCreate::class)->middleware(['can:orders_create'])->name('orders.create');
        Route::get('/orders/{order}/edit', OrderEdit::class)->middleware(['can:orders_edit'])->name('orders.edit');
        Route::get('/orders/tracking/{order?}', OrderTrackingShow::class)->middleware(['can:order_tracking'])
            ->name('orders.tracking.show');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->middleware(['can:orders_show'])->name('orders.invoice');
    });

Route::middleware(['auth', 'verified', 'seller.panel'])
    ->name('seller.')
    ->prefix('/seller')
    ->group(function () {

        Route::get('/orders', SellerOrderList::class)->name('orders.index');
        // Route::get('/orders/{order}/show', OrderShow::class)->name('orders.show');
        Route::get('/orders/create', SellerOrderCreate::class)->name('orders.create');
        Route::get('/orders/{order}/edit', SellerOrderEdit::class)->name('orders.edit');
        Route::get('/orders/{order}/show', SellerOrderShow::class)->name('orders.show');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    });
