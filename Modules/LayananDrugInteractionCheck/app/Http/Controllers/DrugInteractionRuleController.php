<?php

namespace Modules\LayananDrugInteractionCheck\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\LayananDrugInteractionCheck\Http\Requests\StoreDrugInteractionRuleRequest;
use Modules\LayananDrugInteractionCheck\Http\Requests\UpdateDrugInteractionRuleRequest;
use Modules\LayananDrugInteractionCheck\Models\DrugInteractionRule;
use Modules\LayananDrugInteractionCheck\Services\DrugInteractionCheckService;

/**
 * CRUD master rule interaksi. Semua tulis (store/update/destroy) didelegasikan
 * ke service karena ada gerbang bisnis pasangan unordered (A-B == B-A, A != B).
 */
class DrugInteractionRuleController extends Controller
{
    public function __construct(protected DrugInteractionCheckService $service)
    {
    }

    public function index()
    {
        return DrugInteractionRule::query()
            ->with(['itemA:id,name', 'itemB:id,name'])
            ->latest('id')
            ->paginate(15);
    }

    public function show(DrugInteractionRule $drug_interaction_rule): DrugInteractionRule
    {
        return $drug_interaction_rule->loadMissing(['itemA:id,name', 'itemB:id,name']);
    }

    public function store(StoreDrugInteractionRuleRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->storeRule($request->validated())->loadMissing(['itemA:id,name', 'itemB:id,name']),
            201,
        );
    }

    public function update(UpdateDrugInteractionRuleRequest $request, DrugInteractionRule $drug_interaction_rule): DrugInteractionRule
    {
        return $this->service->updateRule($drug_interaction_rule->getKey(), $request->validated())
            ->loadMissing(['itemA:id,name', 'itemB:id,name']);
    }

    public function destroy(DrugInteractionRule $drug_interaction_rule): JsonResponse
    {
        $this->service->deleteRule($drug_interaction_rule->getKey());

        return response()->json(null, 204);
    }
}
