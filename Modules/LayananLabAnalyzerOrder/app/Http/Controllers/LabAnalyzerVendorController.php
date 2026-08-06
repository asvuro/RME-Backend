<?php

namespace Modules\LayananLabAnalyzerOrder\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\LayananLabAnalyzerOrder\Http\Requests\StoreLabAnalyzerVendorRequest;
use Modules\LayananLabAnalyzerOrder\Http\Requests\UpdateLabAnalyzerVendorRequest;
use Modules\LayananLabAnalyzerOrder\Http\Resources\LabAnalyzerVendorResource;
use Modules\LayananLabAnalyzerOrder\Models\LabAnalyzerVendor;
use Modules\LayananLabAnalyzerOrder\Services\LabAnalyzerVendorService;

class LabAnalyzerVendorController extends Controller
{
    public function __construct(protected LabAnalyzerVendorService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return LabAnalyzerVendorResource::collection(
            LabAnalyzerVendor::query()->orderBy('vendor_name')->get(),
        );
    }

    public function store(StoreLabAnalyzerVendorRequest $request): LabAnalyzerVendorResource
    {
        $vendor = $this->service->create($request->validated());

        return (new LabAnalyzerVendorResource($vendor))->response()->setStatusCode(201);
    }

    public function show(LabAnalyzerVendor $lab_analyzer_vendor): LabAnalyzerVendorResource
    {
        return new LabAnalyzerVendorResource($lab_analyzer_vendor);
    }

    public function update(UpdateLabAnalyzerVendorRequest $request, LabAnalyzerVendor $lab_analyzer_vendor): LabAnalyzerVendorResource
    {
        return new LabAnalyzerVendorResource($this->service->update($lab_analyzer_vendor, $request->validated()));
    }

    public function destroy(LabAnalyzerVendor $lab_analyzer_vendor)
    {
        $this->service->destroy($lab_analyzer_vendor);

        return response()->noContent();
    }
}
