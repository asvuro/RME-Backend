<?php

namespace Modules\LayananImagingOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\LayananImagingOrder\Models\ImagingOrder;

class UpdateImagingOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Hanya atribut pemesanan yang boleh lewat edit bebas. 'scheduled_at' dan
     * 'status' sengaja tidak ada: penjadwalan (termasuk jadwal ulang) wajib
     * lewat POST /imaging-orders/{id}/schedule, pembatalan lewat .../cancel —
     * keduanya gerbang di ImagingOrderService.
     */
    public function rules(): array
    {
        return [
            'modality' => ['sometimes', 'string', Rule::in(ImagingOrder::MODALITIES)],
            'body_part' => ['sometimes', 'string', 'max:255'],
            'ordered_by' => ['sometimes', 'integer', 'exists:employees,id'],
            'ordered_at' => ['sometimes', 'date'],
        ];
    }
}
