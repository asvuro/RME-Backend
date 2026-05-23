<?php

namespace Modules\GeneralAccidentGuarantorType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralAccidentGuarantorType\Models\AccidentGuarantorType;

class AccidentGuarantorTypeController extends Controller
{
    public function index()
    {
        return AccidentGuarantorType::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:accident_guarantor_types,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:accident_guarantor_types,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(AccidentGuarantorType::create($data)->refresh(), 201);
    }

    public function show(AccidentGuarantorType $accidentGuarantorType): AccidentGuarantorType
    {
        return $accidentGuarantorType;
    }

    public function update(Request $request, AccidentGuarantorType $accidentGuarantorType): AccidentGuarantorType
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('accident_guarantor_types', 'name')->ignore($accidentGuarantorType->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('accident_guarantor_types', 'code')->ignore($accidentGuarantorType->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $accidentGuarantorType->update($data);

        return $accidentGuarantorType;
    }

    public function destroy(AccidentGuarantorType $accidentGuarantorType)
    {
        $accidentGuarantorType->delete();

        return response()->json(null, 204);
    }
}