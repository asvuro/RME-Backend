<?php

namespace Modules\GeneralContactType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralContactType\Models\ContactType;

class ContactTypeController extends Controller
{
    public function index()
    {
        return ContactType::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:contact_types,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:contact_types,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(ContactType::create($data)->refresh(), 201);
    }

    public function show(ContactType $contactType): ContactType
    {
        return $contactType;
    }

    public function update(Request $request, ContactType $contactType): ContactType
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('contact_types', 'name')->ignore($contactType->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('contact_types', 'code')->ignore($contactType->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $contactType->update($data);

        return $contactType;
    }

    public function destroy(ContactType $contactType)
    {
        $contactType->delete();

        return response()->json(null, 204);
    }
}