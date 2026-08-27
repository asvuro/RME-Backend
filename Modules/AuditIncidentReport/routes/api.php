<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditIncidentReport\Http\Controllers\IncidentReportController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Baca: semua staf terautentikasi.
    Route::apiResource('incident-reports', IncidentReportController::class)->only(['index', 'show']);

    // Tulis: petugas|admin. Kalkulasi grade/SLA + gerbang status ada di
    // IncidentReportService, bukan di controller.
    // Param {incident_report} (15 char) aman dari batas 32 char Symfony.
    Route::apiResource('incident-reports', IncidentReportController::class)->only(['store', 'update']);

    // Transisi state: reported → under_investigation → rca_required → closed.
    Route::post('incident-reports/{incident_report}/investigate', [IncidentReportController::class, 'investigate']);
    Route::post('incident-reports/{incident_report}/rca', [IncidentReportController::class, 'requireRca']);
    Route::post('incident-reports/{incident_report}/close', [IncidentReportController::class, 'close']);
});
