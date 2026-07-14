<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/admin')
    ->middleware(['auth', 'admin.panel'])
    ->group(function () {});
