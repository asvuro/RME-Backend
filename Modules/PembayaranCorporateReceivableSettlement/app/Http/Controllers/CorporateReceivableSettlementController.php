<?php

namespace Modules\PembayaranCorporateReceivableSettlement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PembayaranCorporateReceivable\Models\CorporateReceivable;
use Modules\PembayaranCorporateReceivableSettlement\Http\Requests\StoreCorporateReceivableSettlementRequest;
use Modules\PembayaranCorporateReceivableSettlement\Http\Resources\CorporateReceivableSettlementResource;
use Modules\PembayaranCorporateReceivableSettlement\Models\CorporateReceivableSettlement;

class CorporateReceivableSettlementController extends Controller
{
    public function index(Request $request)
    {
        $query = CorporateReceivableSettlement::query();

        if ($request->filled('corporate_receivable_id')) {
            $query->where('corporate_receivable_id', $request->integer('corporate_receivable_id'));
        }

        return CorporateReceivableSettlementResource::collection($query->latest('paid_at')->paginate($request->integer('per_page', 15)));
    }

    /**
     * Settlement is a financial record - append-only, no update/delete.
     * Marks the parent receivable as settled.
     */
    public function store(StoreCorporateReceivableSettlementRequest $request)
    {
        $data = $request->validated();
        $data['paid_at'] ??= now();
        $data['received_by'] = $request->user()->id;

        $settlement = DB::transaction(function () use ($data) {
            $receivable = CorporateReceivable::query()
                ->whereKey($data['corporate_receivable_id'])
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($receivable->status === 'settled', 422, 'Piutang korporat ini sudah lunas dan tidak dapat dilunasi kembali.');

            $alreadyPaid = (float) CorporateReceivableSettlement::query()
                ->where('corporate_receivable_id', $receivable->id)
                ->sum('paid_amount');

            abort_if(
                $alreadyPaid + (float) $data['paid_amount'] > (float) $receivable->amount,
                422,
                'Total pelunasan melebihi jumlah piutang korporat.'
            );

            $settlement = CorporateReceivableSettlement::create($data);
            $receivable->update(['status' => 'settled']);

            return $settlement;
        });

        return (new CorporateReceivableSettlementResource($settlement))->response()->setStatusCode(201);
    }

    public function show(CorporateReceivableSettlement $corporate_receivable_settlement): CorporateReceivableSettlementResource
    {
        return new CorporateReceivableSettlementResource($corporate_receivable_settlement);
    }
}
