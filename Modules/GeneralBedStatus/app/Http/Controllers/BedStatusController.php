<?php

namespace Modules\GeneralBedStatus\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralBedStatus\Models\BedStatus;

class BedStatusController extends Controller
{
    public function index()
    {
        return BedStatus::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:bed_statuses,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:bed_statuses,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(BedStatus::create($data)->refresh(), 201);
    }

    public function show(BedStatus $bedStatus): BedStatus
    {
        return $bedStatus;
    }

    public function update(Request $request, BedStatus $bedStatus): BedStatus
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('bed_statuses', 'name')->ignore($bedStatus->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('bed_statuses', 'code')->ignore($bedStatus->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $bedStatus->update($data);

        return $bedStatus;
    }

    public function destroy(BedStatus $bedStatus)
    {
        $bedStatus->delete();

        return response()->json(null, 204);
    }
}