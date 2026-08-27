<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralDoctorMedicalDepartment\Http\Controllers\DoctorMedicalDepartmentController;

Route::apiResource('doctor-medical-departments', DoctorMedicalDepartmentController::class)->names('generaldoctormedicaldepartment.doctor-medical-departments')->parameters(['doctor-medical-departments' => 'doctorMedicalDepartment'])->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('doctor-medical-departments', DoctorMedicalDepartmentController::class)->names('generaldoctormedicaldepartment.doctor-medical-departments')->parameters(['doctor-medical-departments' => 'doctorMedicalDepartment'])->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum']);
