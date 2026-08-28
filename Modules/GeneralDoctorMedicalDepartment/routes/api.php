<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralDoctorMedicalDepartment\Http\Controllers\DoctorMedicalDepartmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('doctor-medical-departments', DoctorMedicalDepartmentController::class)->names('generaldoctormedicaldepartment.doctor-medical-departments')->parameters(['doctor-medical-departments' => 'doctorMedicalDepartment'])->only(['index', 'show']);

    Route::apiResource('doctor-medical-departments', DoctorMedicalDepartmentController::class)->names('generaldoctormedicaldepartment.doctor-medical-departments')->parameters(['doctor-medical-departments' => 'doctorMedicalDepartment'])->only(['store', 'update', 'destroy']);
});
