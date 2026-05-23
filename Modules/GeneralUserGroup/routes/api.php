<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralUserGroup\Http\Controllers\UserGroupController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('user-groups', UserGroupController::class);
});