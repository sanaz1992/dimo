<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Livewire\HomePage;

Route::get('/', HomePage::class)->name('home');
