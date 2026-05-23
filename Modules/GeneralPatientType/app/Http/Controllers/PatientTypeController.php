<?php

namespace Modules\GeneralPatientType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralPatientType\Models\PatientType;

class PatientTypeController extends Controller
{
    public function index()
    {
        return PatientType::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:patient_types,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:patient_types,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(PatientType::create($data)->refresh(), 201);
    }

    public function show(PatientType $patientType): PatientType
    {
        return $patientType;
    }

    public function update(Request $request, PatientType $patientType): PatientType
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('patient_types', 'name')->ignore($patientType->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('patient_types', 'code')->ignore($patientType->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $patientType->update($data);

        return $patientType;
    }

    public function destroy(PatientType $patientType)
    {
        $patientType->delete();

        return response()->json(null, 204);
    }
}