<?php

namespace Modules\InventoryWardItemStock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Contracts\WardScope;
use Illuminate\Http\Request;
use Modules\InventoryWardItemStock\Http\Requests\StoreWardItemStockRequest;
use Modules\InventoryWardItemStock\Http\Requests\UpdateWardItemStockRequest;
use Modules\InventoryWardItemStock\Http\Resources\WardItemStockResource;
use Modules\InventoryWardItemStock\Models\WardItemStock;

class InventoryWardItemStockController extends Controller
{
    public function __construct(protected WardScope $wardScope) {}

    public function index(Request $request)
    {
        $query = WardItemStock::query();

        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->integer('ward_id'));
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->integer('item_id'));
        }

        // Cakupan baca lewat kontrak WardScope yang sama dengan show/store/
        // update/destroy (canAccessWard: admin bebas; unassigned = belum masuk
        // rollout, akses penuh; berassignment = ward miliknya saja). Dulu
        // index() menyalin logika ini sendiri dengan semantik berbeda sehingga
        // petugas berassignment bisa melihat stok SEMUA ward lewat list sambil
        // tetap 403 saat membuka satu barisnya.
        $query = $this->wardScope->applyReadScope($query, 'ward_id', $request->user());

        return WardItemStockResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreWardItemStockRequest $request)
    {
        $data = $request->validated();

        abort_if(
            ! $this->wardScope->canAccessWard($request->user(), (int) $data['ward_id']),
            403,
            'Anda tidak ditugaskan ke ward ini.',
        );

        $stock = WardItemStock::create($data);

        return (new WardItemStockResource($stock))->response()->setStatusCode(201);
    }

    public function show(Request $request, WardItemStock $inventorywarditemstock): WardItemStockResource
    {
        abort_if(
            ! $this->wardScope->canAccessWard($request->user(), $inventorywarditemstock->ward_id),
            403,
            'Anda tidak ditugaskan ke ward ini.',
        );

        return new WardItemStockResource($inventorywarditemstock);
    }

    public function update(UpdateWardItemStockRequest $request, WardItemStock $inventorywarditemstock): WardItemStockResource
    {
        abort_if(
            ! $this->wardScope->canAccessWard($request->user(), $inventorywarditemstock->ward_id),
            403,
            'Anda tidak ditugaskan ke ward ini.',
        );

        $inventorywarditemstock->update($request->validated());

        return new WardItemStockResource($inventorywarditemstock);
    }

    public function destroy(Request $request, WardItemStock $inventorywarditemstock)
    {
        abort_if(
            ! $this->wardScope->canAccessWard($request->user(), $inventorywarditemstock->ward_id),
            403,
            'Anda tidak ditugaskan ke ward ini.',
        );

        $inventorywarditemstock->delete();

        return response()->json(null, 204);
    }
}
