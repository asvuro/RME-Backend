<?php

namespace Modules\LayananPatientComplaint\Services;

use Modules\LayananPatientComplaint\Models\PatientSurvey;

/**
 * Survei kepuasan pasien. Gerbang bisnis utamanya: satu kunjungan hanya
 * boleh punya SATU survei. Kalau tidak dijaga di sini, pengisian ganda
 * (double submit dari kiosk/mobile) akan merusak angka indeks kepuasan
 * karena kunjungan yang sama terhitung dua kali - jauh lebih berbahaya
 * daripada sekadar error unique constraint.
 */
class PatientSurveyService
{
    /**
     * Catat survei baru; tolak bila kunjungan sudah pernah disurvei.
     */
    public function create(array $data): PatientSurvey
    {
        abort_if(
            PatientSurvey::query()->where('visit_id', $data['visit_id'])->exists(),
            422,
            'Kunjungan ini sudah memiliki survei kepuasan - satu kunjungan hanya boleh mengisi survei satu kali.',
        );

        return PatientSurvey::create($data);
    }

    /**
     * Perbarui survei (mis. koreksi skor atau melengkapi feedback).
     * Bila visit_id diganti, kunjungan tujuan juga tidak boleh sudah
     * memiliki survei lain supaya gerbang "satu kunjungan satu survei"
     * tetap berlaku meski lewat jalur update.
     */
    public function update(PatientSurvey $survey, array $data): PatientSurvey
    {
        $targetVisitId = $data['visit_id'] ?? $survey->visit_id;

        abort_if(
            $targetVisitId != $survey->visit_id
            && PatientSurvey::query()
                ->where('visit_id', $targetVisitId)
                ->whereKeyNot($survey->id)
                ->exists(),
            422,
            'Kunjungan tujuan sudah memiliki survei kepuasan - satu kunjungan hanya boleh mengisi survei satu kali.',
        );

        $survey->update($data);

        return $survey->refresh();
    }

    public function delete(PatientSurvey $survey): void
    {
        $survey->delete();
    }
}
