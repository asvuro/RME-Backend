<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralDoctorWardAssignment\Http\Controllers\DoctorWardAssignmentController;

Route::apiResource('doctor-ward-assignments', DoctorWardAssignmentController::class)->names('generaldoctorwardassignment.doctor-ward-assignments')->parameters(['doctor-ward-assignments' => 'doctorWardAssignment'])->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('doctor-ward-assignments', DoctorWardAssignmentController::class)->names('generaldoctorwardassignment.doctor-ward-assignments')->parameters(['doctor-ward-assignments' => 'doctorWardAssignment'])->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum']);
