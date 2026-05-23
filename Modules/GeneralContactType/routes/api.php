<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralContactType\Http\Controllers\ContactTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('contact-types', ContactTypeController::class);
});