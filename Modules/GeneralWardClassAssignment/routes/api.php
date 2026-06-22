<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralWardClassAssignment\Http\Controllers\GeneralWardClassAssignmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ward-class-assignments', GeneralWardClassAssignmentController::class)->only(['index', 'show'])->parameters(['ward-class-assignments' => 'wardClassAssignment']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('ward-class-assignments', GeneralWardClassAssignmentController::class)->only(['store', 'update', 'destroy'])->parameters(['ward-class-assignments' => 'wardClassAssignment']);
    });
});
