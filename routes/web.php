<?php

use Illuminate\Support\Facades\Route;

// comment for test
// test// comment for test
// test// comment for test
// test// comment for test
// test

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');
