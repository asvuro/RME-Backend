<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralMedicalDepartmentWardAssignment\Http\Controllers\MedicalDepartmentWardAssignmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('medical-department-ward-assignments', MedicalDepartmentWardAssignmentController::class)->only(['index', 'show'])->parameters(['medical-department-ward-assignments' => 'assignment']);

    Route::apiResource('medical-department-ward-assignments', MedicalDepartmentWardAssignmentController::class)->only(['store', 'update', 'destroy'])->parameters(['medical-department-ward-assignments' => 'assignment']);
});
