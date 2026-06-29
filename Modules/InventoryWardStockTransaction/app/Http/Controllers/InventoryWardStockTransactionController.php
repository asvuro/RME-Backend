<?php

namespace Modules\InventoryWardStockTransaction\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\InventoryWardStockTransaction\Http\Requests\StoreWardStockTransactionRequest;
use Modules\InventoryWardStockTransaction\Http\Resources\WardStockTransactionResource;
use Modules\InventoryWardStockTransaction\Models\WardStockTransaction;
use Modules\InventoryWardStockTransaction\Services\WardStockService;

class InventoryWardStockTransactionController extends Controller
{
    public function __construct(protected WardStockService $wardStockService) {}

    public function index(Request $request)
    {
        $query = WardStockTransaction::query();

        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->integer('ward_id'));
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->integer('item_id'));
        }

        return WardStockTransactionResource::collection($query->latest('performed_at')->paginate($request->integer('per_page', 15)));
    }

    /**
     * A transaction is append-only, same as PendaftaranHistory - it is the ledger entry.
     * The running balance lives in InventoryWardItemStock and is adjusted here.
     */
    public function store(StoreWardStockTransactionRequest $request)
    {
        $data = $request->validated();

        $transaction = $this->wardStockService->record(
            wardId: $data['ward_id'],
            itemId: $data['item_id'],
            type: $data['type'],
            quantity: $data['quantity'],
            user: $request->user(),
            notes: $data['notes'] ?? null,
            performedAt: isset($data['performed_at']) ? new \DateTimeImmutable($data['performed_at']) : null,
        );

        return (new WardStockTransactionResource($transaction))->response()->setStatusCode(201);
    }

    public function show(WardStockTransaction $ward_stock_transaction): WardStockTransactionResource
    {
        return new WardStockTransactionResource($ward_stock_transaction);
    }
}
