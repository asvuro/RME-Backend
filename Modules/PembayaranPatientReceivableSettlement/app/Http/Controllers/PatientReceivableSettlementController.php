<?php

namespace Modules\PembayaranPatientReceivableSettlement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PembayaranPatientReceivable\Models\PatientReceivable;
use Modules\PembayaranPatientReceivableSettlement\Http\Requests\StorePatientReceivableSettlementRequest;
use Modules\PembayaranPatientReceivableSettlement\Http\Resources\PatientReceivableSettlementResource;
use Modules\PembayaranPatientReceivableSettlement\Models\PatientReceivableSettlement;

class PatientReceivableSettlementController extends Controller
{
    public function index(Request $request)
    {
        $query = PatientReceivableSettlement::query();

        if ($request->filled('patient_receivable_id')) {
            $query->where('patient_receivable_id', $request->integer('patient_receivable_id'));
        }

        return PatientReceivableSettlementResource::collection($query->latest('paid_at')->paginate($request->integer('per_page', 15)));
    }

    /**
     * Settlement is a financial record - append-only, no update/delete.
     * Marks the parent receivable as settled.
     */
    public function store(StorePatientReceivableSettlementRequest $request)
    {
        $data = $request->validated();
        $data['paid_at'] ??= now();
        $data['received_by'] = $request->user()->id;

        $settlement = DB::transaction(function () use ($data) {
            $receivable = PatientReceivable::query()
                ->whereKey($data['patient_receivable_id'])
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($receivable->status === 'settled', 422, 'Piutang pasien ini sudah lunas dan tidak dapat dilunasi kembali.');

            $alreadyPaid = (float) PatientReceivableSettlement::query()
                ->where('patient_receivable_id', $receivable->id)
                ->sum('paid_amount');

            abort_if(
                $alreadyPaid + (float) $data['paid_amount'] > (float) $receivable->amount,
                422,
                'Total pelunasan melebihi jumlah piutang pasien.'
            );

            $settlement = PatientReceivableSettlement::create($data);
            $receivable->update(['status' => 'settled']);

            return $settlement;
        });

        return (new PatientReceivableSettlementResource($settlement))->response()->setStatusCode(201);
    }

    public function show(PatientReceivableSettlement $patient_receivable_settlement): PatientReceivableSettlementResource
    {
        return new PatientReceivableSettlementResource($patient_receivable_settlement);
    }
}
