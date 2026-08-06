<?php

namespace Modules\InventoryLinenTracking\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\InventoryLinenTracking\Http\Requests\StoreLinenCycleRequest;
use Modules\InventoryLinenTracking\Http\Requests\UpdateLinenCycleRequest;
use Modules\InventoryLinenTracking\Http\Resources\LinenCycleResource;
use Modules\InventoryLinenTracking\Models\LinenCycle;
use Modules\InventoryLinenTracking\Services\LinenCycleService;

class LinenCycleController extends Controller
{
    public function __construct(protected LinenCycleService $linenCycleService)
    {
    }

    public function index(Request $request)
    {
        $query = LinenCycle::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('ward_id')) {
            $wardId = $request->integer('ward_id');
            $query->whereHas('linenItem', fn ($q) => $q->where('ward_id', $wardId));
        }

        return LinenCycleResource::collection($query->latest()->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreLinenCycleRequest $request)
    {
        $cycle = $this->linenCycleService->create($request->validated());

        return (new LinenCycleResource($cycle))->response()->setStatusCode(201);
    }

    public function show(LinenCycle $linen_cycle): LinenCycleResource
    {
        return new LinenCycleResource($linen_cycle);
    }

    public function update(UpdateLinenCycleRequest $request, LinenCycle $linen_cycle): LinenCycleResource
    {
        $cycle = $this->linenCycleService->update($linen_cycle->id, $request->validated());

        return new LinenCycleResource($cycle);
    }

    public function destroy(LinenCycle $linen_cycle)
    {
        $this->linenCycleService->delete($linen_cycle->id);

        return response()->json(null, 204);
    }
}
