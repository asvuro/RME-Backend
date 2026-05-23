<?php

namespace Modules\GeneralInsuranceCardType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralInsuranceCardType\Models\InsuranceCardType;

class InsuranceCardTypeController extends Controller
{
    public function index()
    {
        return InsuranceCardType::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:insurance_card_types,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:insurance_card_types,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(InsuranceCardType::create($data)->refresh(), 201);
    }

    public function show(InsuranceCardType $insuranceCardType): InsuranceCardType
    {
        return $insuranceCardType;
    }

    public function update(Request $request, InsuranceCardType $insuranceCardType): InsuranceCardType
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('insurance_card_types', 'name')->ignore($insuranceCardType->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('insurance_card_types', 'code')->ignore($insuranceCardType->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $insuranceCardType->update($data);

        return $insuranceCardType;
    }

    public function destroy(InsuranceCardType $insuranceCardType)
    {
        $insuranceCardType->delete();

        return response()->json(null, 204);
    }
}