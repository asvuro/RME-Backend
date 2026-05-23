<?php

namespace Modules\GeneralSitbChildTbScore6\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralSitbChildTbScore6\Models\SitbChildTbScore6;

class SitbChildTbScore6Controller extends Controller
{
    public function index()
    {
        return SitbChildTbScore6::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sitb_child_tb_score6s,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:sitb_child_tb_score6s,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(SitbChildTbScore6::create($data)->refresh(), 201);
    }

    public function show(SitbChildTbScore6 $sitbChildTbScore6): SitbChildTbScore6
    {
        return $sitbChildTbScore6;
    }

    public function update(Request $request, SitbChildTbScore6 $sitbChildTbScore6): SitbChildTbScore6
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('sitb_child_tb_score6s', 'name')->ignore($sitbChildTbScore6->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('sitb_child_tb_score6s', 'code')->ignore($sitbChildTbScore6->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $sitbChildTbScore6->update($data);

        return $sitbChildTbScore6;
    }

    public function destroy(SitbChildTbScore6 $sitbChildTbScore6)
    {
        $sitbChildTbScore6->delete();

        return response()->json(null, 204);
    }
}