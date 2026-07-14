<?php

use Illuminate\Support\Facades\Route;
use Modules\ACL\Http\Controllers\ACLController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('acls', ACLController::class)->names('acl');
});
