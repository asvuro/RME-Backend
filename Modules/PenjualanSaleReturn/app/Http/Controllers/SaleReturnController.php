<?php

namespace Modules\PenjualanSaleReturn\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PenjualanSale\Models\Sale;
use Modules\PenjualanSaleReturn\Http\Requests\StoreSaleReturnRequest;
use Modules\PenjualanSaleReturn\Http\Resources\SaleReturnResource;
use Modules\PenjualanSaleReturn\Models\SaleReturn;
use Modules\PenjualanSaleReturn\Models\SaleReturnItem;

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
     *
     * Refund amounts are derived server-side from what was actually sold:
     * every line must reference a sale item of THIS sale, the returned
     * quantity is capped by sold quantity minus already returned units, and
     * each line's refund equals quantity x sold unit price (bcmath, exact to
     * cents). Zero-value returns are rejected.
     */
    public function store(StoreSaleReturnRequest $request)
    {
        $validated = $request->validated();

        $return = DB::transaction(function () use ($validated) {
            $sale = Sale::query()
                ->whereKey($validated['sale_id'])
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($sale->items()->doesntExist(), 422, 'Penjualan tidak memiliki item yang bisa diretur.');

            // Index sold lines once; an id outside this sale is rejected below.
            $soldItems = $sale->items->keyBy('id');

            $requestedBySaleItem = collect($validated['items'])
                ->groupBy('sale_item_id')
                ->map(fn ($lines) => (int) $lines->sum('quantity'));

            $alreadyReturned = SaleReturnItem::query()
                ->whereIn('sale_item_id', $soldItems->keys())
                ->selectRaw('sale_item_id, SUM(quantity) as returned_quantity')
                ->groupBy('sale_item_id')
                ->pluck('returned_quantity', 'sale_item_id');

            $lines = [];
            foreach ($requestedBySaleItem as $saleItemId => $quantity) {
                /** @var \Modules\PenjualanSaleItem\Models\SaleItem|null $soldItem */
                $soldItem = $soldItems->get($saleItemId);
                abort_if($soldItem === null, 422, "Item penjualan #{$saleItemId} bukan bagian dari penjualan ini.");

                $remaining = (int) $soldItem->quantity - (int) $alreadyReturned->get($saleItemId, 0);
                if ($quantity > $remaining) {
                    abort(422, "Jumlah retur untuk item penjualan #{$saleItemId} melebihi sisa barang yang bisa diretur.");
                }

                $lines[] = [
                    'sale_item_id' => $soldItem->id,
                    'quantity' => $quantity,
                    'refunded_amount' => bcmul((string) $quantity, (string) $soldItem->unit_price, 2),
                ];
            }

            $refundAmount = bcadd('0', '0', 2);
            foreach ($lines as $line) {
                $refundAmount = bcadd($refundAmount, $line['refunded_amount'], 2);
            }

            // A return row that moves no money is ledger noise.
            abort_if(bccomp($refundAmount, '0', 2) === 0, 422, 'Nilai retur tidak boleh nol.');

            $alreadyRefunded = (float) SaleReturn::query()
                ->where('sale_id', $sale->id)
                ->sum('refund_amount');

            abort_if(
                $alreadyRefunded + (float) $refundAmount > (float) $sale->total_amount,
                422,
                'Total retur melebihi nilai penjualan.'
            );

            $return = SaleReturn::create([
                'sale_id' => $sale->id,
                'returned_at' => $validated['returned_at'] ?? now(),
                'reason' => $validated['reason'] ?? null,
                'refund_amount' => $refundAmount,
            ]);

            $return->items()->createMany($lines);

            return $return;
        });

        return (new SaleReturnResource($return))->response()->setStatusCode(201);
    }

    public function show(SaleReturn $saleReturn): SaleReturnResource
    {
        return new SaleReturnResource($saleReturn);
    }
}
