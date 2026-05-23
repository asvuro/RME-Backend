<?php

namespace Modules\GeneralSitbDiagnosisType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralSitbDiagnosisType\Models\SitbDiagnosisType;

class SitbDiagnosisTypeController extends Controller
{
    public function index()
    {
        return SitbDiagnosisType::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sitb_diagnosis_types,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:sitb_diagnosis_types,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(SitbDiagnosisType::create($data)->refresh(), 201);
    }

    public function show(SitbDiagnosisType $sitbDiagnosisType): SitbDiagnosisType
    {
        return $sitbDiagnosisType;
    }

    public function update(Request $request, SitbDiagnosisType $sitbDiagnosisType): SitbDiagnosisType
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('sitb_diagnosis_types', 'name')->ignore($sitbDiagnosisType->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('sitb_diagnosis_types', 'code')->ignore($sitbDiagnosisType->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $sitbDiagnosisType->update($data);

        return $sitbDiagnosisType;
    }

    public function destroy(SitbDiagnosisType $sitbDiagnosisType)
    {
        $sitbDiagnosisType->delete();

        return response()->json(null, 204);
    }
}