<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralStaffMember\Http\Controllers\StaffMemberController;

Route::apiResource('staff-members', StaffMemberController::class)->names('generalstaffmember.staff-members')->parameters(['staff-members' => 'staffMember'])->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('staff-members', StaffMemberController::class)->names('generalstaffmember.staff-members')->parameters(['staff-members' => 'staffMember'])->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum']);
