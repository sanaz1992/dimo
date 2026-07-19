<?php

use Illuminate\Support\Facades\Route;



Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
});
