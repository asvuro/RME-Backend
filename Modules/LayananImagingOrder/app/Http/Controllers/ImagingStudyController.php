<?php

namespace Modules\LayananImagingOrder\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\LayananImagingOrder\Http\Requests\StoreImagingStudyRequest;
use Modules\LayananImagingOrder\Http\Requests\UpdateImagingStudyRequest;
use Modules\LayananImagingOrder\Http\Resources\ImagingStudyResource;
use Modules\LayananImagingOrder\Models\ImagingStudy;
use Modules\LayananImagingOrder\Services\ImagingStudyService;

/**
 * Controller studi imaging. store()/update() wajib lewat ImagingStudyService:
 * pencatatan studi adalah gerbang transisi order → completed, bukan sekadar
 * insert baris.
 */
class ImagingStudyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ImagingStudy::query();

        if ($request->filled('imaging_order_id')) {
            $query->where('imaging_order_id', $request->input('imaging_order_id'));
        }

        return ImagingStudyResource::collection(
            $query->latest('id')->paginate($request->integer('per_page', 15)),
        );
    }

    public function store(StoreImagingStudyRequest $request, ImagingStudyService $service): JsonResponse
    {
        $study = $service->record($request->validated());

        return (new ImagingStudyResource($study))->response()->setStatusCode(201);
    }

    public function show(ImagingStudy $imaging_study): ImagingStudyResource
    {
        return new ImagingStudyResource($imaging_study);
    }

    public function update(
        UpdateImagingStudyRequest $request,
        ImagingStudy $imaging_study,
        ImagingStudyService $service,
    ): ImagingStudyResource {
        return new ImagingStudyResource($service->updateDetails($imaging_study, $request->validated()));
    }
}
