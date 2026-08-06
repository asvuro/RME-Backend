<?php

namespace Modules\LayananImagingOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImagingStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imaging_order_id' => ['required', 'integer', 'exists:imaging_orders,id'],
            // Placeholder UID untuk PACS nyata di masa depan; unik bila terisi.
            'study_instance_uid' => ['nullable', 'string', 'max:255', 'unique:imaging_studies,study_instance_uid'],
            'performed_at' => ['required', 'date'],
            'findings_summary' => ['nullable', 'string'],
            // Path relatif storage internal juga sah (bukan hanya URL absolut),
            // jadi divalidasi sebagai string, bukan rule 'url'.
            'report_url' => ['nullable', 'string', 'max:255'],
        ];
    }
}
