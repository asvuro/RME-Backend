<?php

namespace Modules\GeneralFacilityMaintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceWorkOrder;

/**
 * Hanya field deskriptif yang boleh diubah lewat update biasa. 'status',
 * 'assigned_to', dan 'completed_at' sengaja TIDAK divalidasi di sini —
 * perubahannya hanya lewat MaintenanceWorkOrderService::assign()/complete()
 * supaya gerbang state machine tidak bisa dilewati lewat PUT biasa.
 */
class UpdateMaintenanceWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'issue_description' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'string', 'in:' . implode(',', [
                MaintenanceWorkOrder::PRIORITY_LOW,
                MaintenanceWorkOrder::PRIORITY_MEDIUM,
                MaintenanceWorkOrder::PRIORITY_HIGH,
                MaintenanceWorkOrder::PRIORITY_CRITICAL,
            ])],
        ];
    }
}
