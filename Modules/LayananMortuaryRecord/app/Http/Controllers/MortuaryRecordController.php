<?php

namespace Modules\LayananMortuaryRecord\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LayananMortuaryRecord\Http\Requests\ReleaseMortuaryRecordRequest;
use Modules\LayananMortuaryRecord\Http\Requests\StoreMortuaryRecordRequest;
use Modules\LayananMortuaryRecord\Http\Requests\UpdateMortuaryRecordRequest;
use Modules\LayananMortuaryRecord\Http\Resources\MortuaryRecordResource;
use Modules\LayananMortuaryRecord\Models\MortuaryRecord;
use Modules\LayananMortuaryRecord\Services\MortuaryRecordService;

class MortuaryRecordController extends Controller
{
    public function __construct(protected MortuaryRecordService $service) {}

    public function index(Request $request)
    {
        $query = MortuaryRecord::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return MortuaryRecordResource::collection(
            $query->orderBy('id', 'desc')->paginate($request->integer('per_page', 15))
        );
    }

    public function store(StoreMortuaryRecordRequest $request)
    {
        $record = $this->service->admit($request->validated());

        return (new MortuaryRecordResource($record))->response()->setStatusCode(201);
    }

    public function show(MortuaryRecord $record): MortuaryRecordResource
    {
        return new MortuaryRecordResource($record);
    }

    public function update(UpdateMortuaryRecordRequest $request, MortuaryRecord $record): MortuaryRecordResource
    {
        $record = $this->service->updateDetails($record, $request->validated());

        return new MortuaryRecordResource($record);
    }

    public function destroy(MortuaryRecord $record)
    {
        $this->service->deleteRecord($record);

        return response()->json(null, 204);
    }

    /** Gerbang rilis jenazah: hanya boleh dari status in_mortuary. */
    public function release(ReleaseMortuaryRecordRequest $request, MortuaryRecord $record): MortuaryRecordResource
    {
        $record = $this->service->release($record, $request->validated());

        return new MortuaryRecordResource($record);
    }
}
