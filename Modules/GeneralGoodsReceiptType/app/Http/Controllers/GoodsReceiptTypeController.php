<?php

namespace Modules\GeneralGoodsReceiptType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralGoodsReceiptType\Models\GoodsReceiptType;

class GoodsReceiptTypeController extends Controller
{
    public function index()
    {
        return GoodsReceiptType::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:goods_receipt_types,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:goods_receipt_types,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(GoodsReceiptType::create($data)->refresh(), 201);
    }

    public function show(GoodsReceiptType $goodsReceiptType): GoodsReceiptType
    {
        return $goodsReceiptType;
    }

    public function update(Request $request, GoodsReceiptType $goodsReceiptType): GoodsReceiptType
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('goods_receipt_types', 'name')->ignore($goodsReceiptType->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('goods_receipt_types', 'code')->ignore($goodsReceiptType->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $goodsReceiptType->update($data);

        return $goodsReceiptType;
    }

    public function destroy(GoodsReceiptType $goodsReceiptType)
    {
        $goodsReceiptType->delete();

        return response()->json(null, 204);
    }
}