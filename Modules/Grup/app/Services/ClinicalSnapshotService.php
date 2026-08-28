<?php

namespace Modules\Grup\Services;

use Illuminate\Support\Facades\DB;
use Modules\GeneralPatient\Models\Patient;

class ClinicalSnapshotService
{
    public function patient(Patient $patient, bool $includeClinical = true): array
    {
        $data = [
            'id' => (string) $patient->id,
            'medical_record_number' => $patient->medical_record_number,
            'name' => $patient->name,
            'birth_place' => $patient->birth_place,
            'birth_date' => $patient->birth_date?->toDateString(),
            'gender_id' => $patient->gender_id,
            'address' => $patient->address,
            'updated_at' => $patient->updated_at?->toIso8601String(),
        ];

        if (! $includeClinical) {
            return $data;
        }

        $limit = min(max((int) config('grup.max_clinical_rows', 100), 1), 500);
        $visitIds = DB::table('visits')->join('registrations', 'registrations.id', '=', 'visits.registration_id')
            ->where('registrations.patient_id', $patient->id)->pluck('visits.id');

        $data['clinical'] = [
            'allergies' => DB::table('allergies')->where('patient_id', $patient->id)
                ->orderByDesc('id')->limit($limit)
                ->get(['category', 'allergen', 'reaction', 'severity', 'is_active', 'created_at'])->all(),
            'diagnoses' => DB::table('diagnoses')->whereIn('visit_id', $visitIds)
                ->orderByDesc('recorded_at')->limit($limit)
                ->get(['visit_id', 'diagnosis_code_id', 'is_primary', 'recorded_at', 'status'])->all(),
            'vital_signs' => DB::table('vital_signs')->whereIn('visit_id', $visitIds)
                ->orderByDesc('recorded_at')->limit($limit)
                ->get(['visit_id', 'recorded_at', 'temperature', 'pulse', 'respiratory_rate', 'systolic', 'diastolic', 'oxygen_saturation', 'pain_scale'])->all(),
            'clinical_notes' => DB::table('clinical_notes')->whereIn('visit_id', $visitIds)
                ->where('status', 'active')->orderByDesc('recorded_at')->limit($limit)
                ->get(['visit_id', 'recorded_at', 'subjective', 'objective', 'assessment', 'planning', 'instructions', 'note_type'])->all(),
        ];

        return $data;
    }
}
