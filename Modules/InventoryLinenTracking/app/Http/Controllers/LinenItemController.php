<?php

namespace Modules\InventoryLinenTracking\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\InventoryLinenTracking\Http\Requests\StoreLinenItemRequest;
use Modules\InventoryLinenTracking\Http\Requests\UpdateLinenItemRequest;
use Modules\InventoryLinenTracking\Http\Resources\LinenItemResource;
use Modules\InventoryLinenTracking\Models\LinenItem;

class LinenItemController extends Controller
{
    public function index(Request $request)
    {
        $query = LinenItem::query();

        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->integer('ward_id'));
        }

        if ($request->filled('linen_type')) {
            $query->where('linen_type', $request->string('linen_type'));
        }

        return LinenItemResource::collection($query->latest()->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreLinenItemRequest $request)
    {
        $item = LinenItem::create($request->validated());

        return (new LinenItemResource($item))->response()->setStatusCode(201);
    }

    public function show(LinenItem $linen_item): LinenItemResource
    {
        return new LinenItemResource($linen_item);
    }

    public function update(UpdateLinenItemRequest $request, LinenItem $linen_item): LinenItemResource
    {
        $linen_item->update($request->validated());

        return new LinenItemResource($linen_item);
    }

    public function destroy(LinenItem $linen_item)
    {
        $linen_item->delete();

        return response()->json(null, 204);
    }
}
