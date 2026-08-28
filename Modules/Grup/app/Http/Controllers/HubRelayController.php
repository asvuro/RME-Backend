<?php

namespace Modules\Grup\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\GeneralPatient\Models\Patient;
use Modules\Grup\Models\Branch;
use Modules\Grup\Models\GroupReferral;
use Modules\Grup\Services\ClinicalSnapshotService;

class HubRelayController extends Controller
{
    public function patients(Request $request, ClinicalSnapshotService $snapshots): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);
        $needle = addcslashes($data['q'], '%_\\');
        $patients = Patient::query()->where('is_active', true)
            ->where(fn ($q) => $q->where('name', 'like', "%{$needle}%")
                ->orWhere('medical_record_number', 'like', "%{$needle}%"))
            ->orderBy('name')->paginate(min((int) ($data['per_page'] ?? 20), 50));

        return response()->json([
            'data' => collect($patients->items())->map(fn (Patient $patient) => $snapshots->patient($patient, false)),
            'meta' => ['current_page' => $patients->currentPage(), 'last_page' => $patients->lastPage(), 'total' => $patients->total()],
        ]);
    }

    public function patient(Patient $patient, ClinicalSnapshotService $snapshots): JsonResponse
    {
        abort_unless($patient->is_active, 404);

        return response()->json(['data' => $snapshots->patient($patient)]);
    }

    public function referral(string $referralId): JsonResponse
    {
        $local = Branch::where('is_local', true)->where('status', 'active')->firstOrFail();
        $referral = GroupReferral::where('hub_referral_id', $referralId)->where('group_id', $local->group_id)
            ->where(fn ($q) => $q->where('source_branch_id', $local->id)->orWhere('destination_branch_id', $local->id))->firstOrFail();

        return response()->json(['data' => [
            'id' => $referral->hub_referral_id,
            'source_branch_id' => $referral->sourceBranch->hub_branch_id,
            'destination_branch_id' => $referral->destinationBranch->hub_branch_id,
            'source_patient_id' => $referral->source_patient_id,
            'patient_snapshot' => $referral->patient_snapshot,
            'reason' => $referral->reason,
            'clinical_summary' => $referral->clinical_summary,
            'status' => $referral->status,
            'referred_at' => $referral->referred_at?->toIso8601String(),
        ]]);
    }
}
