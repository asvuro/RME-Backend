<?php

namespace Modules\InventorySterilizationCycle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\InventorySterilizationCycle\Http\Requests\StoreSterilizationCycleRequest;
use Modules\InventorySterilizationCycle\Http\Requests\UpdateSterilizationCycleRequest;
use Modules\InventorySterilizationCycle\Models\SterilizationCycle;
use Modules\InventorySterilizationCycle\Services\SterilizationCycleService;

class SterilizationCycleController extends Controller
{
    public function __construct(protected SterilizationCycleService $cycleService) {}

    public function index(Request $request)
    {
        $query = SterilizationCycle::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return $query->latest('started_at')->paginate($request->integer('per_page', 15));
    }

    public function store(StoreSterilizationCycleRequest $request)
    {
        $cycle = $this->cycleService->createCycle($request->validated());

        return response()->json($cycle->refresh(), 201);
    }

    public function show(SterilizationCycle $sterilization_cycle): SterilizationCycle
    {
        return $sterilization_cycle;
    }

    public function update(UpdateSterilizationCycleRequest $request, SterilizationCycle $sterilization_cycle): SterilizationCycle
    {
        return $this->cycleService->updateCycle($sterilization_cycle, $request->validated());
    }

    public function destroy(SterilizationCycle $sterilization_cycle)
    {
        $this->cycleService->deleteCycle($sterilization_cycle);

        return response()->json(null, 204);
    }
}
