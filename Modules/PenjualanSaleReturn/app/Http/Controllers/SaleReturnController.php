<?php

namespace Modules\PenjualanSaleReturn\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PenjualanSale\Models\Sale;
use Modules\PenjualanSaleReturn\Http\Requests\StoreSaleReturnRequest;
use Modules\PenjualanSaleReturn\Http\Resources\SaleReturnResource;
use Modules\PenjualanSaleReturn\Models\SaleReturn;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = SaleReturn::query();

        if ($request->filled('sale_id')) {
            $query->where('sale_id', $request->integer('sale_id'));
        }

        return SaleReturnResource::collection($query->latest('returned_at')->paginate($request->integer('per_page', 15)));
    }

    /**
     * Sale returns are a financial record - append-only, no update/delete.
     */
    public function store(StoreSaleReturnRequest $request)
    {
        $data = $request->validated();
        $data['returned_at'] ??= now();

        $return = DB::transaction(function () use ($data) {
            $sale = Sale::query()
                ->whereKey($data['sale_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $alreadyRefunded = (float) SaleReturn::query()
                ->where('sale_id', $sale->id)
                ->sum('refund_amount');

            abort_if(
                $alreadyRefunded + (float) $data['refund_amount'] > (float) $sale->total_amount,
                422,
                'Total retur melebihi nilai penjualan.'
            );

            return SaleReturn::create($data);
        });

        return (new SaleReturnResource($return))->response()->setStatusCode(201);
    }

    public function show(SaleReturn $saleReturn): SaleReturnResource
    {
        return new SaleReturnResource($saleReturn);
    }
}
