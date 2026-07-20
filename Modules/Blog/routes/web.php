<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Livewire\BlogList;

Route::get('/blogs', BlogList::class)->name('blogs.index');
