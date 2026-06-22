<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralStaffWardAssignment\Http\Controllers\StaffWardAssignmentController;

Route::apiResource('staff-ward-assignments', StaffWardAssignmentController::class)->names('generalstaffwardassignment.staff-ward-assignments')->parameters(['staff-ward-assignments' => 'staffWardAssignment'])->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('staff-ward-assignments', StaffWardAssignmentController::class)->names('generalstaffwardassignment.staff-ward-assignments')->parameters(['staff-ward-assignments' => 'staffWardAssignment'])->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum', 'role:petugas|admin']);
