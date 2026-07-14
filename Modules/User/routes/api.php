<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\AdminController;
use Modules\User\Http\Controllers\Api\CityController;
use Modules\User\Http\Controllers\Api\CustomerLevelController;
use Modules\User\Http\Controllers\Api\ProvinceController;
use Modules\User\Http\Controllers\UserController;

// Route::middleware(['auth:guest'])->group(function () {
Route::apiResource('users', UserController::class)->names('user');
// });

Route::prefix('/admin')
    ->middleware(['auth:sanctum', 'admin.panel'])
    ->group(function () {

        Route::apiResource('/admins', AdminController::class);
        Route::post('/admins/{user}/avatar', [AdminController::class, 'updateUserImage']);
        Route::patch('/admins/{user}/restore', [AdminController::class, 'restore']);
        Route::get('/admins/{user}/roles', [AdminController::class, 'getAdminRoles']);
        Route::put('/admins/{user}/roles', [AdminController::class, 'updateAdminRoles']);
        Route::get('/admins/{user}/permissions', [AdminController::class, 'getAllAdminPermissions']);

        Route::get('/get-admin-levels', [AdminController::class, 'getAdminLevels']);

        Route::apiResource('/customer-levels', CustomerLevelController::class);
    });

Route::get('/provinces', [ProvinceController::class, 'index']);
Route::get('/cities', [CityController::class, 'index']);
