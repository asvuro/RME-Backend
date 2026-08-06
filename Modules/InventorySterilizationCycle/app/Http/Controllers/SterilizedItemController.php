<?php

namespace Modules\InventorySterilizationCycle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\InventorySterilizationCycle\Http\Requests\StoreSterilizedItemRequest;
use Modules\InventorySterilizationCycle\Http\Requests\UpdateSterilizedItemRequest;
use Modules\InventorySterilizationCycle\Models\SterilizedItem;
use Modules\InventorySterilizationCycle\Services\SterilizedItemService;

class SterilizedItemController extends Controller
{
    public function __construct(protected SterilizedItemService $itemService) {}

    public function index(Request $request)
    {
        $query = SterilizedItem::query();

        if ($request->filled('cycle_id')) {
            $query->where('cycle_id', $request->integer('cycle_id'));
        }

        return $query->latest()->paginate($request->integer('per_page', 15));
    }

    public function store(StoreSterilizedItemRequest $request)
    {
        $data = $request->validated();
        $item = $this->itemService->createItem((int) $data['cycle_id'], $data);

        return response()->json($item->refresh(), 201);
    }

    public function show(SterilizedItem $sterilized_item): SterilizedItem
    {
        return $sterilized_item;
    }

    public function update(UpdateSterilizedItemRequest $request, SterilizedItem $sterilized_item): SterilizedItem
    {
        return $this->itemService->updateItem($sterilized_item, $request->validated());
    }

    public function destroy(SterilizedItem $sterilized_item)
    {
        $sterilized_item->delete();

        return response()->json(null, 204);
    }

    /**
     * GET sterilized-items/{sterilized_item}/check-expiry — cek kedaluwarsa
     * berdasarkan expiry_date yang sudah dihitung saat item dibuat.
     */
    public function checkExpiry(SterilizedItem $sterilized_item)
    {
        return response()->json([
            'id' => $sterilized_item->id,
            'expiry_date' => $sterilized_item->expiry_date->toDateString(),
            'expired' => $this->itemService->checkExpiry($sterilized_item),
        ]);
    }
}
