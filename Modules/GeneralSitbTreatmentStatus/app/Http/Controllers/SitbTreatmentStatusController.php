<?php

namespace Modules\GeneralSitbTreatmentStatus\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralSitbTreatmentStatus\Models\SitbTreatmentStatus;

class SitbTreatmentStatusController extends Controller
{
    public function index()
    {
        return SitbTreatmentStatus::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sitb_treatment_statuses,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:sitb_treatment_statuses,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(SitbTreatmentStatus::create($data)->refresh(), 201);
    }

    public function show(SitbTreatmentStatus $sitbTreatmentStatus): SitbTreatmentStatus
    {
        return $sitbTreatmentStatus;
    }

    public function update(Request $request, SitbTreatmentStatus $sitbTreatmentStatus): SitbTreatmentStatus
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('sitb_treatment_statuses', 'name')->ignore($sitbTreatmentStatus->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('sitb_treatment_statuses', 'code')->ignore($sitbTreatmentStatus->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $sitbTreatmentStatus->update($data);

        return $sitbTreatmentStatus;
    }

    public function destroy(SitbTreatmentStatus $sitbTreatmentStatus)
    {
        $sitbTreatmentStatus->delete();

        return response()->json(null, 204);
    }
}