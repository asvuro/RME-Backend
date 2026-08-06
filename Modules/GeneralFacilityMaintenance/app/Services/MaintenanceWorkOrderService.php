<?php

namespace Modules\GeneralFacilityMaintenance\Services;

use Illuminate\Support\Facades\DB;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceAsset;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceWorkOrder;

/**
 * Gerbang state machine work order IPSRS.
 *
 * Alur status: open -> in_progress (assign) -> completed (complete), atau
 * dibatalkan (cancelled, di luar scope service ini karena spesifikasi tidak
 * memintanya). complete() HANYA boleh dari in_progress.
 */
class MaintenanceWorkOrderService
{
    /**
     * Buat work order baru. Ditolak bila asset sudah decommissioned (tidak
     * masuk akal membuat tiket perbaikan utk aset yang sudah dinonaktifkan).
     * Asset yang masih operational otomatis dipindah ke under_repair — status
     * kembali ke operational hanya lewat complete() (kecuali priority critical).
     */
    public function createWorkOrder(array $data): MaintenanceWorkOrder
    {
        return DB::transaction(function () use ($data) {
            $asset = MaintenanceAsset::query()->lockForUpdate()->findOrFail($data['asset_id']);

            abort_if(
                $asset->status === MaintenanceAsset::STATUS_DECOMMISSIONED,
                422,
                'Asset sudah decommissioned, tidak bisa dibuat work order baru.',
            );

            $workOrder = MaintenanceWorkOrder::create([
                'asset_id' => $asset->id,
                'reported_by' => $data['reported_by'],
                'issue_description' => $data['issue_description'],
                'priority' => $data['priority'] ?? MaintenanceWorkOrder::PRIORITY_MEDIUM,
                'status' => MaintenanceWorkOrder::STATUS_OPEN,
                'reported_at' => $data['reported_at'] ?? now(),
            ]);

            if ($asset->status === MaintenanceAsset::STATUS_OPERATIONAL) {
                $asset->update(['status' => MaintenanceAsset::STATUS_UNDER_REPAIR]);
            }

            return $workOrder;
        });
    }

    /**
     * Tugaskan (atau tugaskan ulang) petugas ke work order. Ditolak bila
     * work order sudah completed/cancelled — tiket yang sudah tutup tidak
     * boleh ditugaskan lagi. Work order open otomatis pindah ke in_progress;
     * work order yang sudah in_progress boleh ditugaskan ulang (reassign)
     * tanpa mengubah statusnya.
     */
    public function assign(int $workOrderId, int $employeeId): MaintenanceWorkOrder
    {
        return DB::transaction(function () use ($workOrderId, $employeeId) {
            $workOrder = MaintenanceWorkOrder::query()->lockForUpdate()->findOrFail($workOrderId);

            abort_if(
                in_array($workOrder->status, [MaintenanceWorkOrder::STATUS_COMPLETED, MaintenanceWorkOrder::STATUS_CANCELLED], true),
                422,
                "Work order #{$workOrderId} sudah {$workOrder->status}, tidak bisa ditugaskan.",
            );

            $workOrder->update([
                'assigned_to' => $employeeId,
                'status' => MaintenanceWorkOrder::STATUS_IN_PROGRESS,
            ]);

            return $workOrder;
        });
    }

    /**
     * Selesaikan work order. Gerbang: hanya boleh dari in_progress — open
     * (belum ada yang mengerjakan) atau completed/cancelled (sudah tutup)
     * ditolak 422.
     *
     * Efek pada asset: normalnya asset otomatis kembali operational.
     * KECUALI priority critical — kerusakan kritikal butuh verifikasi manual
     * (mis. QA/K3) sebelum aset dianggap aman dipakai lagi, jadi asset TIDAK
     * disentuh dan requires_manual_verification ditandai true pada work
     * order-nya.
     */
    public function complete(int $workOrderId): MaintenanceWorkOrder
    {
        return DB::transaction(function () use ($workOrderId) {
            $workOrder = MaintenanceWorkOrder::query()->lockForUpdate()->with('asset')->findOrFail($workOrderId);

            abort_if(
                $workOrder->status !== MaintenanceWorkOrder::STATUS_IN_PROGRESS,
                422,
                "Work order #{$workOrderId} hanya bisa diselesaikan dari status in_progress.",
            );

            $isCritical = $workOrder->priority === MaintenanceWorkOrder::PRIORITY_CRITICAL;

            $workOrder->update([
                'status' => MaintenanceWorkOrder::STATUS_COMPLETED,
                'completed_at' => now(),
                'requires_manual_verification' => $isCritical,
            ]);

            if (! $isCritical && $workOrder->asset !== null) {
                $workOrder->asset->update(['status' => MaintenanceAsset::STATUS_OPERATIONAL]);
            }

            return $workOrder;
        });
    }
}
