<?php

namespace Modules\LayananPatientComplaint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\LayananPatientComplaint\Http\Requests\StorePatientComplaintRequest;
use Modules\LayananPatientComplaint\Http\Requests\UpdatePatientComplaintRequest;
use Modules\LayananPatientComplaint\Models\PatientComplaint;
use Modules\LayananPatientComplaint\Services\PatientComplaintService;

/**
 * CRUD komplain pasien + rekap per status. Semua penulisan lewat
 * PatientComplaintService - controller sengaja tidak pernah memanggil
 * Model::create()/update() langsung karena transisi status punya gerbang
 * prasyarat (handled_by utk diproses, resolution_notes utk selesai).
 */
class PatientComplaintController extends Controller
{
    public function __construct(protected PatientComplaintService $service) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            PatientComplaint::query()->orderByDesc('id')->paginate($request->integer('per_page', 15)),
        );
    }

    /**
     * Penyimpanan selalu melahirkan status 'baru': field status tidak
     * diterima dari klien (lihat StorePatientComplaintRequest), dan service
     * menimpanya dengan STATUS_BARU sebagai pengaman kedua.
     */
    public function store(StorePatientComplaintRequest $request): JsonResponse
    {
        return response()->json($this->service->create($request->validated())->refresh(), 201);
    }

    public function show(PatientComplaint $patientComplaint): PatientComplaint
    {
        return $patientComplaint;
    }

    public function update(UpdatePatientComplaintRequest $request, PatientComplaint $patientComplaint): PatientComplaint
    {
        return $this->service->update($patientComplaint, $request->validated());
    }

    public function destroy(PatientComplaint $patientComplaint): Response
    {
        $this->service->delete($patientComplaint);

        return response()->noContent();
    }

    /**
     * Rekap jumlah komplain per status. Tanpa query param mengembalikan
     * ketiga status (yang kosong tetap muncul bernilai 0); dengan
     * ?status= hanya mengembalikan hitungan status tersebut.
     */
    public function summary(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->service->summaryCounts($request->query('status')),
        ]);
    }
}
