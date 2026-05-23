<?php

namespace Modules\GeneralGoodsReceiptCancellationReason\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\GeneralGoodsReceiptCancellationReason\Models\GoodsReceiptCancellationReason;

class GoodsReceiptCancellationReasonController extends Controller
{
    public function index()
    {
        return GoodsReceiptCancellationReason::query()->orderBy('name')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:goods_receipt_cancellation_reasons,name'],
            'code' => ['nullable', 'string', 'max:10', 'unique:goods_receipt_cancellation_reasons,code'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return response()->json(GoodsReceiptCancellationReason::create($data)->refresh(), 201);
    }

    public function show(GoodsReceiptCancellationReason $record): GoodsReceiptCancellationReason
    {
        return $record;
    }

    public function update(Request $request, GoodsReceiptCancellationReason $record): GoodsReceiptCancellationReason
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('goods_receipt_cancellation_reasons', 'name')->ignore($record->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('goods_receipt_cancellation_reasons', 'code')->ignore($record->id)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $record->update($data);

        return $record;
    }

    public function destroy(GoodsReceiptCancellationReason $record)
    {
        $record->delete();

        return response()->json(null, 204);
    }
}