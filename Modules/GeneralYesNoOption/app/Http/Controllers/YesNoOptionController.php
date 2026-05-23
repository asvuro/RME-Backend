<?php

namespace Modules\GeneralYesNoOption\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralYesNoOption\Models\YesNoOption;

class YesNoOptionController extends Controller
{
    public function index()
    {
        return YesNoOption::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:yes_no_options,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:yes_no_options,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(YesNoOption::create($data)->refresh(), 201);
    }

    public function show(YesNoOption $yesNoOption): YesNoOption
    {
        return $yesNoOption;
    }

    public function update(Request $request, YesNoOption $yesNoOption): YesNoOption
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('yes_no_options', 'name')->ignore($yesNoOption->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('yes_no_options', 'code')->ignore($yesNoOption->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $yesNoOption->update($data);

        return $yesNoOption;
    }

    public function destroy(YesNoOption $yesNoOption)
    {
        $yesNoOption->delete();

        return response()->json(null, 204);
    }
}