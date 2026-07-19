<?php

use Illuminate\Support\Facades\Route;
use Modules\Pages\Http\Controllers\PagesController;
use Modules\Pages\Http\Livewire\About;
use Modules\Pages\Http\Livewire\Contactus;

Route::get('/about', About::class)->name('about.index');
Route::get('/contact-us', Contactus::class)->name('contactus.index');
