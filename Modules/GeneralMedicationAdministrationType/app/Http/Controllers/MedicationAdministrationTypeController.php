<?php

namespace Modules\GeneralMedicationAdministrationType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralMedicationAdministrationType\Models\MedicationAdministrationType;

class MedicationAdministrationTypeController extends Controller
{
    public function index()
    {
        return MedicationAdministrationType::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:medication_administration_types,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:medication_administration_types,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(MedicationAdministrationType::create($data)->refresh(), 201);
    }

    public function show(MedicationAdministrationType $medicationAdministrationType): MedicationAdministrationType
    {
        return $medicationAdministrationType;
    }

    public function update(Request $request, MedicationAdministrationType $medicationAdministrationType): MedicationAdministrationType
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('medication_administration_types', 'name')->ignore($medicationAdministrationType->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('medication_administration_types', 'code')->ignore($medicationAdministrationType->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $medicationAdministrationType->update($data);

        return $medicationAdministrationType;
    }

    public function destroy(MedicationAdministrationType $medicationAdministrationType)
    {
        $medicationAdministrationType->delete();

        return response()->json(null, 204);
    }
}