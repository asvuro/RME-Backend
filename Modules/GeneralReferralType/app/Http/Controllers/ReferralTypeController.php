<?php

namespace Modules\GeneralReferralType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralReferralType\Models\ReferralType;

class ReferralTypeController extends Controller
{
    public function index()
    {
        return ReferralType::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:referral_types,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:referral_types,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(ReferralType::create($data)->refresh(), 201);
    }

    public function show(ReferralType $referralType): ReferralType
    {
        return $referralType;
    }

    public function update(Request $request, ReferralType $referralType): ReferralType
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('referral_types', 'name')->ignore($referralType->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('referral_types', 'code')->ignore($referralType->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $referralType->update($data);

        return $referralType;
    }

    public function destroy(ReferralType $referralType)
    {
        $referralType->delete();

        return response()->json(null, 204);
    }
}