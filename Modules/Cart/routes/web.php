<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Livewire\CartPage;

Route::get('/cart', CartPage::class)->name('cart.index');
