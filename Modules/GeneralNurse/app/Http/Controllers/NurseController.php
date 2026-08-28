<?php

namespace Modules\GeneralNurse\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\GeneralNurse\Models\Nurse;
use Modules\GeneralNurse\Http\Requests\StoreNurseRequest;
use Modules\GeneralNurse\Http\Requests\UpdateNurseRequest;
use Modules\GeneralNurse\Http\Resources\NurseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NurseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return NurseResource::collection(Nurse::latest()->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreNurseRequest $request): NurseResource
    {
        $nurse = Nurse::create($request->validated());
        return new NurseResource($nurse);
    }

    public function show(Nurse $nurse): NurseResource
    {
        return new NurseResource($nurse);
    }

    public function update(UpdateNurseRequest $request, Nurse $nurse): NurseResource
    {
        $nurse->update($request->validated());
        return new NurseResource($nurse);
    }

    public function destroy(Nurse $nurse): Response
    {
        $nurse->delete();
        return response()->noContent();
    }
}
