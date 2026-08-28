<?php

namespace Modules\GeneralDoctorWardAssignment\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\GeneralDoctorWardAssignment\Models\DoctorWardAssignment;
use Modules\GeneralDoctorWardAssignment\Http\Requests\StoreDoctorWardAssignmentRequest;
use Modules\GeneralDoctorWardAssignment\Http\Requests\UpdateDoctorWardAssignmentRequest;
use Modules\GeneralDoctorWardAssignment\Http\Resources\DoctorWardAssignmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DoctorWardAssignmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return DoctorWardAssignmentResource::collection(DoctorWardAssignment::latest()->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreDoctorWardAssignmentRequest $request): DoctorWardAssignmentResource
    {
        $assignment = DoctorWardAssignment::create($request->validated());
        return new DoctorWardAssignmentResource($assignment);
    }

    public function show(DoctorWardAssignment $doctorWardAssignment): DoctorWardAssignmentResource
    {
        return new DoctorWardAssignmentResource($doctorWardAssignment);
    }

    public function update(UpdateDoctorWardAssignmentRequest $request, DoctorWardAssignment $doctorWardAssignment): DoctorWardAssignmentResource
    {
        $doctorWardAssignment->update($request->validated());
        return new DoctorWardAssignmentResource($doctorWardAssignment);
    }

    public function destroy(DoctorWardAssignment $doctorWardAssignment): Response
    {
        $doctorWardAssignment->delete();
        return response()->noContent();
    }
}
