<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditActivityLog\Http\Controllers\ActivityLogController;

// Gerbang peran role:admin lama sudah digantikan RoutePermissionGate global
// (RBAC dinamis, per-aksi) -- lihat rbac-dynamic-permission-plan.
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('activity-logs', [ActivityLogController::class, 'index']);
});
