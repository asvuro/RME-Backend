<?php

namespace Modules\AuditIncidentReport\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\AuditIncidentReport\Models\IncidentReport;

/**
 * Domain service Insiden Keselamatan Pasien (IKP).
 *
 * Dua kalkulasi inti yang HARUS lewat sini (tidak boleh dari input klien):
 *   1. gradeFromScores() — matriks risiko 5x5 standar RS:
 *      skor = impact x probability → BIRU (1-3), HIJAU (4-6),
 *      KUNING (8-12), MERAH (15-25). Pengecualian severity-first: dampak
 *      major/catastrophic (impact >= 4) dengan skor <= 6 dinaikkan ke
 *      KUNING, karena satu kejadian berdampak besar tidak boleh tergrading
 *      "moderat" walau probabilitasnya kecil.
 *   2. slaDueAtFor() — tenggat penanganan: BIRU/HIJAU = occurred_at + 14
 *      hari; KUNING/MERAH, serta SEMUA SENTINEL (apa pun skornya),
 *      = occurred_at + 45 hari.
 */
class IncidentReportService
{
    /**
     * Grading deterministik dari matriks 5x5. Murni fungsi skor — tanpa IO,
     * sehingga aman dipanggil statis dari factory/test.
     */
    public static function gradeFromScores(int $impact, int $probability): string
    {
        $score = $impact * $probability;

        // Severity-first: kombinasi "jarang tapi fatal" (4x1=4, 5x1=5)
        // tidak boleh jatuh di HIJAU.
        if ($impact >= 4 && $score <= 6) {
            return IncidentReport::GRADE_KUNING;
        }

        return match (true) {
            $score <= 3 => IncidentReport::GRADE_BIRU,
            $score <= 6 => IncidentReport::GRADE_HIJAU,
            $score <= 12 => IncidentReport::GRADE_KUNING,
            default => IncidentReport::GRADE_MERAH, // 15-25
        };
    }

    /** Tenggat SLA penanganan sesuai grade/kategori. */
    public static function slaDueAtFor(string $grade, string $category, CarbonInterface $occurredAt): Carbon
    {
        // Sentinel selalu jalur panjang 45 hari meski skornya rendah —
        // wajib investigasi penuh menurut standar IKP.
        $days = ($category === IncidentReport::CATEGORY_SENTINEL || in_array($grade, [IncidentReport::GRADE_KUNING, IncidentReport::GRADE_MERAH], true))
            ? 45
            : 14;

        return $occurredAt->copy()->addDays($days);
    }

    /**
     * Simpan laporan baru; risk_grade/status/sla_due_at dihitung di sini,
     * bukan dari payload (field tsb tidak ada di FormRequest).
     *
     * @param  array<string, mixed>  $data  hasil validasi StoreIncidentReportRequest
     */
    public function create(array $data): IncidentReport
    {
        return DB::transaction(function () use ($data) {
            $occurredAt = Carbon::parse($data['occurred_at']);
            $grade = static::gradeFromScores((int) $data['impact_score'], (int) $data['probability_score']);

            $report = new IncidentReport($data);
            $report->forceFill([
                'risk_grade' => $grade,
                'status' => IncidentReport::STATUS_REPORTED,
                'sla_due_at' => static::slaDueAtFor($grade, $data['incident_category'], $occurredAt),
            ]);
            $report->save();

            return $report;
        });
    }

    /**
     * Sunting atribut laporan yang belum ditutup. Grade & SLA ikut
     * dihitung ulang bila skor/occurred_at/kategori berubah — status TIDAK
     * bisa disuntik lewat sini (hanya lewat transisi investigate/rca/close).
     *
     * @param  array<string, mixed>  $data  hasil validasi UpdateIncidentReportRequest
     */
    public function updateDetails(IncidentReport $report, array $data): IncidentReport
    {
        abort_if(
            $report->status === IncidentReport::STATUS_CLOSED,
            422,
            'Laporan yang sudah ditutup tidak dapat disunting.',
        );

        return DB::transaction(function () use ($report, $data) {
            $report->fill($data);

            $grade = static::gradeFromScores($report->impact_score, $report->probability_score);
            $report->forceFill([
                'risk_grade' => $grade,
                'sla_due_at' => static::slaDueAtFor($grade, $report->incident_category, $report->occurred_at),
            ]);
            $report->save();

            return $report->refresh();
        });
    }

    /** Transisi reported → under_investigation. */
    public function startInvestigation(IncidentReport $report): IncidentReport
    {
        abort_if(
            $report->status !== IncidentReport::STATUS_REPORTED,
            422,
            'Hanya laporan berstatus reported yang dapat mulai diselidiki.',
        );

        $report->forceFill(['status' => IncidentReport::STATUS_UNDER_INVESTIGATION])->save();

        return $report->refresh();
    }

    /** Transisi under_investigation → rca_required (analisis akar masalah). */
    public function markRcaRequired(IncidentReport $report): IncidentReport
    {
        abort_if(
            $report->status !== IncidentReport::STATUS_UNDER_INVESTIGATION,
            422,
            'RCA hanya dapat dipicu dari laporan yang sedang diselidiki.',
        );

        $report->forceFill(['status' => IncidentReport::STATUS_RCA_REQUIRED])->save();

        return $report->refresh();
    }

    /**
     * Penutupan: laporan yang belum pernah diselidiki tidak boleh ditutup
     * langsung — jejak audit IKP butuh minimal satu tahap investigasi.
     */
    public function close(IncidentReport $report): IncidentReport
    {
        abort_if(
            $report->status === IncidentReport::STATUS_REPORTED,
            422,
            'Laporan harus diselidiki terlebih dahulu sebelum ditutup.',
        );
        abort_if(
            $report->status === IncidentReport::STATUS_CLOSED,
            422,
            'Laporan sudah berstatus closed.',
        );

        $report->forceFill(['status' => IncidentReport::STATUS_CLOSED])->save();

        return $report->refresh();
    }
}
