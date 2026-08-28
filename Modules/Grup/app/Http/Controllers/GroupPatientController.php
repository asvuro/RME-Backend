<?php

namespace Modules\Grup\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Grup\Http\Requests\SearchGroupPatientRequest;
use Modules\Grup\Models\Branch;
use Modules\Grup\Services\GroupHubClient;

class GroupPatientController extends Controller
{
    public function index(SearchGroupPatientRequest $request, GroupHubClient $hub): JsonResponse
    {
        $query = $request->validated();
        if (isset($query['branch_id'])) {
            $this->activeSibling($query['branch_id']);
        }

        return response()->json($hub->searchPatients($query));
    }

    public function show(string $branchId, string $patientId, GroupHubClient $hub): JsonResponse
    {
        $this->activeSibling($branchId);
        abort_unless(preg_match('/^[A-Za-z0-9._-]{1,100}$/', $patientId), 422, 'ID pasien tidak valid.');

        return response()->json($hub->patient($branchId, $patientId));
    }

    private function activeSibling(string $hubBranchId): Branch
    {
        $local = Branch::where('is_local', true)->where('status', 'active')->firstOrFail();

        return Branch::where('hub_branch_id', $hubBranchId)
            ->where('group_id', $local->group_id)->where('status', 'active')->where('is_local', false)->firstOrFail();
    }
}
