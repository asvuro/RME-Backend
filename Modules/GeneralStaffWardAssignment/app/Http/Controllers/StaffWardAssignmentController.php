<?php

namespace Modules\GeneralStaffWardAssignment\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\GeneralStaffWardAssignment\Models\StaffWardAssignment;
use Modules\GeneralStaffWardAssignment\Http\Requests\StoreStaffWardAssignmentRequest;
use Modules\GeneralStaffWardAssignment\Http\Requests\UpdateStaffWardAssignmentRequest;
use Modules\GeneralStaffWardAssignment\Http\Resources\StaffWardAssignmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class StaffWardAssignmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StaffWardAssignmentResource::collection(StaffWardAssignment::latest()->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreStaffWardAssignmentRequest $request): StaffWardAssignmentResource
    {
        $assignment = StaffWardAssignment::create($request->validated());
        return new StaffWardAssignmentResource($assignment);
    }

    public function show(StaffWardAssignment $staffWardAssignment): StaffWardAssignmentResource
    {
        return new StaffWardAssignmentResource($staffWardAssignment);
    }

    public function update(UpdateStaffWardAssignmentRequest $request, StaffWardAssignment $staffWardAssignment): StaffWardAssignmentResource
    {
        $staffWardAssignment->update($request->validated());
        return new StaffWardAssignmentResource($staffWardAssignment);
    }

    public function destroy(StaffWardAssignment $staffWardAssignment): Response
    {
        $staffWardAssignment->delete();
        return response()->noContent();
    }
}
