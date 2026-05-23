<?php

use Illuminate\Support\Facades\Route;
use Modules\DashboardCore\Http\Controllers\DashboardCoreController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('dashboard/core', [DashboardCoreController::class, 'core']);
});
