<?php

namespace Modules\GeneralVisitStatus\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralVisitStatus\Models\VisitStatus;

class VisitStatusController extends Controller
{
    public function index()
    {
        return VisitStatus::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:visit_statuses,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:visit_statuses,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(VisitStatus::create($data)->refresh(), 201);
    }

    public function show(VisitStatus $visitStatus): VisitStatus
    {
        return $visitStatus;
    }

    public function update(Request $request, VisitStatus $visitStatus): VisitStatus
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('visit_statuses', 'name')->ignore($visitStatus->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('visit_statuses', 'code')->ignore($visitStatus->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $visitStatus->update($data);

        return $visitStatus;
    }

    public function destroy(VisitStatus $visitStatus)
    {
        $visitStatus->delete();

        return response()->json(null, 204);
    }
}