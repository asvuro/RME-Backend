<?php

namespace Modules\GeneralFacilityMaintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceAsset;

class StoreMaintenanceAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_code' => ['required', 'string', 'max:255', 'unique:maintenance_assets,asset_code'],
            'asset_name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
            'status' => ['sometimes', 'string', 'in:' . implode(',', [
                MaintenanceAsset::STATUS_OPERATIONAL,
                MaintenanceAsset::STATUS_UNDER_REPAIR,
                MaintenanceAsset::STATUS_DECOMMISSIONED,
            ])],
        ];
    }
}
