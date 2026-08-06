<?php

namespace Modules\LayananImagingOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\LayananImagingOrder\Models\ImagingOrder;

class StoreImagingOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 'status' sengaja TIDAK divalidasi/diterima: order baru selalu mulai dari
     * 'ordered' lewat ImagingOrderService::create() — state machine tidak boleh
     * bisa dilewati dari input pembuat.
     */
    public function rules(): array
    {
        return [
            'visit_id' => ['required', 'integer', 'exists:visits,id'],
            'modality' => ['required', 'string', Rule::in(ImagingOrder::MODALITIES)],
            'body_part' => ['required', 'string', 'max:255'],
            // Wajib di API meski kolom nullable: akuntabilitas pemesan
            // (kolom nullable hanya untuk toleransi data impor lama).
            'ordered_by' => ['required', 'integer', 'exists:employees,id'],
            'ordered_at' => ['required', 'date'],
        ];
    }
}
