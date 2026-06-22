<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralNurseWardAssignment\Http\Controllers\NurseWardAssignmentController;

Route::apiResource('nurse-ward-assignments', NurseWardAssignmentController::class)->names('generalnursewardassignment.nurse-ward-assignments')->parameters(['nurse-ward-assignments' => 'nurseWardAssignment'])->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('nurse-ward-assignments', NurseWardAssignmentController::class)->names('generalnursewardassignment.nurse-ward-assignments')->parameters(['nurse-ward-assignments' => 'nurseWardAssignment'])->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum', 'role:petugas|admin']);
