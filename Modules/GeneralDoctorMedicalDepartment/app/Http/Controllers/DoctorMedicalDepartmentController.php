<?php

namespace Modules\GeneralDoctorMedicalDepartment\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\GeneralDoctorMedicalDepartment\Models\DoctorMedicalDepartment;
use Modules\GeneralDoctorMedicalDepartment\Http\Requests\StoreDoctorMedicalDepartmentRequest;
use Modules\GeneralDoctorMedicalDepartment\Http\Requests\UpdateDoctorMedicalDepartmentRequest;
use Modules\GeneralDoctorMedicalDepartment\Http\Resources\DoctorMedicalDepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DoctorMedicalDepartmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return DoctorMedicalDepartmentResource::collection(DoctorMedicalDepartment::latest()->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreDoctorMedicalDepartmentRequest $request): DoctorMedicalDepartmentResource
    {
        $assignment = DoctorMedicalDepartment::create($request->validated());
        return new DoctorMedicalDepartmentResource($assignment);
    }

    public function show(DoctorMedicalDepartment $doctorMedicalDepartment): DoctorMedicalDepartmentResource
    {
        return new DoctorMedicalDepartmentResource($doctorMedicalDepartment);
    }

    public function update(UpdateDoctorMedicalDepartmentRequest $request, DoctorMedicalDepartment $doctorMedicalDepartment): DoctorMedicalDepartmentResource
    {
        $doctorMedicalDepartment->update($request->validated());
        return new DoctorMedicalDepartmentResource($doctorMedicalDepartment);
    }

    public function destroy(DoctorMedicalDepartment $doctorMedicalDepartment): Response
    {
        $doctorMedicalDepartment->delete();
        return response()->noContent();
    }
}
