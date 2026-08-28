<?php

use Illuminate\Support\Facades\Route;
use Modules\Grup\Http\Controllers\GroupContextController;
use Modules\Grup\Http\Controllers\GroupPatientController;
use Modules\Grup\Http\Controllers\GroupReferralController;
use Modules\Grup\Http\Controllers\HubRelayController;
use Modules\Grup\Http\Controllers\RealtimeNotificationController;

Route::middleware(['auth:sanctum'])->prefix('v1/grup')->group(function () {
    Route::get('context', [GroupContextController::class, 'show']);
    Route::post('context/sync', [GroupContextController::class, 'sync'])->middleware('throttle:10,1');
    Route::get('patients', [GroupPatientController::class, 'index'])->middleware('throttle:60,1');
    Route::get('patients/{branchId}/{patientId}', [GroupPatientController::class, 'show'])->middleware('throttle:60,1');
    Route::get('referrals', [GroupReferralController::class, 'index']);
    Route::post('referrals', [GroupReferralController::class, 'store'])->middleware('throttle:30,1');
    Route::patch('referrals/{referralId}/status', [GroupReferralController::class, 'update'])->middleware('throttle:30,1');
    Route::get('events', [RealtimeNotificationController::class, 'index'])->middleware('throttle:60,1');
});

// Machine-to-machine ingress. RoutePermissionGate mengklasifikasikan ini public
// karena bukan Sanctum user route, tetapi request tetap fail-closed oleh HMAC,
// timestamp, nonce, group ID, dan target instance checks di middleware khusus.
Route::middleware(['grup.hub-signature', 'throttle:120,1'])->prefix('v1/grup/relay')->group(function () {
    Route::get('patients', [HubRelayController::class, 'patients']);
    Route::get('patients/{patient}', [HubRelayController::class, 'patient'])->whereNumber('patient');
    Route::get('referrals/{referralId}', [HubRelayController::class, 'referral']);
    Route::post('notifications', [RealtimeNotificationController::class, 'store']);
});
