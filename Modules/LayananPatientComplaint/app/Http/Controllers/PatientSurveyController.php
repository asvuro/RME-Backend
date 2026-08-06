<?php

namespace Modules\LayananPatientComplaint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\LayananPatientComplaint\Http\Requests\StorePatientSurveyRequest;
use Modules\LayananPatientComplaint\Http\Requests\UpdatePatientSurveyRequest;
use Modules\LayananPatientComplaint\Models\PatientSurvey;
use Modules\LayananPatientComplaint\Services\PatientSurveyService;

/**
 * CRUD survei kepuasan pasien. Penulisan lewat PatientSurveyService agar
 * gerbang "satu kunjungan satu survei" tidak bisa dilewati controller.
 */
class PatientSurveyController extends Controller
{
    public function __construct(protected PatientSurveyService $service) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            PatientSurvey::query()->orderByDesc('id')->paginate($request->integer('per_page', 15)),
        );
    }

    public function store(StorePatientSurveyRequest $request): JsonResponse
    {
        return response()->json($this->service->create($request->validated())->refresh(), 201);
    }

    public function show(PatientSurvey $patientSurvey): PatientSurvey
    {
        return $patientSurvey;
    }

    public function update(UpdatePatientSurveyRequest $request, PatientSurvey $patientSurvey): PatientSurvey
    {
        return $this->service->update($patientSurvey, $request->validated());
    }

    public function destroy(PatientSurvey $patientSurvey): Response
    {
        $this->service->delete($patientSurvey);

        return response()->noContent();
    }
}
