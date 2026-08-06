<?php

namespace Modules\AuditIncidentReport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\AuditIncidentReport\Models\IncidentReport;

class UpdateIncidentReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sama dgn store tapi semua opsional (PATCH-style). status/risk_grade/
     * sla_due_at tetap di luar: transisi status hanya lewat endpoint
     * investigate/rca/close, grade & SLA selalu dihitung ulang service.
     */
    public function rules(): array
    {
        return [
            'visit_id' => ['nullable', 'integer', 'exists:visits,id'],
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'incident_category' => ['sometimes', 'string', 'in:' . implode(',', IncidentReport::CATEGORIES)],
            'description' => ['sometimes', 'string', 'max:65535'],
            'occurred_at' => ['sometimes', 'date'],
            'reported_by' => ['sometimes', 'integer', 'exists:employees,id'],
            'impact_score' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'probability_score' => ['sometimes', 'integer', 'min:1', 'max:5'],
        ];
    }
}
