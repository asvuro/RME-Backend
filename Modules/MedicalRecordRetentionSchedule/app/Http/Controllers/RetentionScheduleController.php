<?php

namespace Modules\MedicalRecordRetentionSchedule\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\MedicalRecordRetentionSchedule\Http\Resources\RetentionScheduleResource;
use Modules\MedicalRecordRetentionSchedule\Models\RetentionSchedule;

/**
 * Read-only (role:admin) — lihat routes/api.php. Tidak ada store/update/
 * destroy lewat HTTP: baris diisi oleh command `retention:scan` dan status
 * ditinjau oleh RetentionScheduleService, bukan input pengguna.
 */
class RetentionScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = RetentionSchedule::query();

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->integer('patient_id'));
        }

        if ($request->filled('registration_id')) {
            $query->where('registration_id', $request->integer('registration_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return RetentionScheduleResource::collection(
            $query->latest('retention_due_at')->paginate($request->integer('per_page', 15))
        );
    }

    public function show(RetentionSchedule $retentionSchedule): RetentionScheduleResource
    {
        return new RetentionScheduleResource($retentionSchedule);
    }
}
