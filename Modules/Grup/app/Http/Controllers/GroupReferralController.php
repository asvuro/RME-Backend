<?php

namespace Modules\Grup\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\GeneralPatient\Models\Patient;
use Modules\Grup\Http\Requests\StoreGroupReferralRequest;
use Modules\Grup\Http\Requests\UpdateGroupReferralStatusRequest;
use Modules\Grup\Models\Branch;
use Modules\Grup\Models\GroupReferral;
use Modules\Grup\Services\ClinicalSnapshotService;
use Modules\Grup\Services\GroupHubClient;

class GroupReferralController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $local = Branch::where('is_local', true)->firstOrFail();
        $query = GroupReferral::with(['sourceBranch:id,name,code,hub_branch_id', 'destinationBranch:id,name,code,hub_branch_id'])
            ->where('group_id', $local->group_id)
            ->where(fn ($q) => $q->where('source_branch_id', $local->id)->orWhere('destination_branch_id', $local->id));

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return response()->json(['data' => $query->latest('referred_at')->paginate(min($request->integer('per_page', 20), 50))]);
    }

    public function store(StoreGroupReferralRequest $request, GroupHubClient $hub, ClinicalSnapshotService $snapshots): JsonResponse
    {
        $data = $request->validated();
        $local = Branch::where('is_local', true)->where('status', 'active')->firstOrFail();
        $destination = Branch::where('hub_branch_id', $data['destination_branch_id'])
            ->where('group_id', $local->group_id)->where('status', 'active')->where('is_local', false)->firstOrFail();
        $patient = Patient::findOrFail($data['patient_id']);
        $idempotencyKey = (string) Str::uuid();

        $remote = $hub->createReferral([
            'source_branch_id' => $local->hub_branch_id,
            'destination_branch_id' => $destination->hub_branch_id,
            'source_patient_id' => (string) $patient->id,
            'patient_snapshot' => $snapshots->patient($patient, false),
            'reason' => $data['reason'],
            'clinical_summary' => $data['clinical_summary'] ?? null,
            'referred_at' => now()->toIso8601String(),
        ], $idempotencyKey);

        $referral = GroupReferral::updateOrCreate(['hub_referral_id' => $remote['id']], [
            'group_id' => $local->group_id,
            'source_branch_id' => $local->id,
            'destination_branch_id' => $destination->id,
            'local_patient_id' => $patient->id,
            'source_patient_id' => (string) $patient->id,
            'patient_snapshot' => $snapshots->patient($patient, false),
            'reason' => $data['reason'],
            'clinical_summary' => $data['clinical_summary'] ?? null,
            'status' => $remote['status'] ?? 'requested',
            'created_by' => $request->user()->id,
            'referred_at' => $remote['referred_at'] ?? now(),
        ]);

        return response()->json(['data' => $referral], 201);
    }

    public function update(UpdateGroupReferralStatusRequest $request, string $referralId, GroupHubClient $hub): JsonResponse
    {
        $local = Branch::where('is_local', true)->where('status', 'active')->firstOrFail();
        $referral = GroupReferral::where('hub_referral_id', $referralId)->where('group_id', $local->group_id)
            ->where(fn ($q) => $q->where('source_branch_id', $local->id)->orWhere('destination_branch_id', $local->id))->firstOrFail();

        $payload = $request->validated();
        $allowed = $referral->source_branch_id === $local->id
            ? ['requested' => ['cancelled']]
            : [
                'requested' => ['accepted', 'rejected'],
                'accepted' => ['in_progress'],
                'in_progress' => ['completed'],
            ];
        abort_unless(in_array($payload['status'], $allowed[$referral->status] ?? [], true), 422, 'Transisi status rujukan tidak diizinkan.');

        $remote = $hub->updateReferral($referralId, $payload, (string) Str::uuid());
        $referral->update(['status' => $remote['status']]);

        return response()->json(['data' => $referral->fresh()]);
    }
}
