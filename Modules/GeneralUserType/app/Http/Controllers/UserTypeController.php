<?php

namespace Modules\GeneralUserType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralUserType\Models\UserType;

class UserTypeController extends Controller
{
    public function index()
    {
        return UserType::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:user_types,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:user_types,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(UserType::create($data)->refresh(), 201);
    }

    public function show(UserType $userType): UserType
    {
        return $userType;
    }

    public function update(Request $request, UserType $userType): UserType
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('user_types', 'name')->ignore($userType->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('user_types', 'code')->ignore($userType->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $userType->update($data);

        return $userType;
    }

    public function destroy(UserType $userType)
    {
        $userType->delete();

        return response()->json(null, 204);
    }
}