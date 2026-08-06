<?php

namespace Modules\AuditQualityIndicator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AuditQualityIndicator\Http\Requests\StoreQualityIndicatorRecordRequest;
use Modules\AuditQualityIndicator\Http\Requests\UpdateQualityIndicatorRecordRequest;
use Modules\AuditQualityIndicator\Models\QualityIndicatorRecord;
use Modules\AuditQualityIndicator\Services\QualityIndicatorRecordService;

class QualityIndicatorRecordController extends Controller
{
    public function __construct(protected QualityIndicatorRecordService $service) {}

    public function index(Request $request)
    {
        $query = QualityIndicatorRecord::query()->with('indicator');

        if ($request->filled('indicator_id')) {
            $query->where('indicator_id', $request->integer('indicator_id'));
        }

        if ($request->filled('year')) {
            $query->where('period_year', $request->integer('year'));
        }

        if ($request->filled('month')) {
            $query->where('period_month', $request->integer('month'));
        }

        return $query
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->paginate($request->integer('per_page', 15));
    }

    public function store(StoreQualityIndicatorRecordRequest $request): JsonResponse
    {
        // Gerbang periode (tidak boleh masa depan, satu catatan per bulan)
        // hanya ada di service — controller tidak pernah create() langsung.
        $record = $this->service->save($request->validated(), $request->user());

        return response()->json($record->refresh(), 201);
    }

    public function show(QualityIndicatorRecord $quality_indicator_record): QualityIndicatorRecord
    {
        return $quality_indicator_record;
    }

    public function update(
        UpdateQualityIndicatorRecordRequest $request,
        QualityIndicatorRecord $quality_indicator_record,
    ): QualityIndicatorRecord {
        return $this->service->update($quality_indicator_record, $request->validated(), $request->user());
    }

    public function destroy(QualityIndicatorRecord $quality_indicator_record): JsonResponse
    {
        // Koreksi salah input dibebaskan (hard delete) — capaian bulanan bukan
        // dokumen legal; riwayat auditnya tetap tertinggal di activity log.
        $this->service->delete($quality_indicator_record);

        return response()->json(null, 204);
    }
}
