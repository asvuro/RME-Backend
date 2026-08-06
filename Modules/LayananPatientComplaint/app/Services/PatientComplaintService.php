<?php

namespace Modules\LayananPatientComplaint\Services;

use Illuminate\Support\Facades\DB;
use Modules\LayananPatientComplaint\Models\PatientComplaint;

/**
 * Siklus hidup komplain pasien: semua penulisan status lewat sini, tidak pernah
 * langsung dari controller. State machine sengaja dibuat maju satu langkah saja
 * (baru -> diproses -> selesai) karena setiap tahap menuntut bukti kerja yang
 * berbeda: "diproses" harus tahu siapa petugas penanggung jawabnya, "selesai"
 * harus meninggalkan catatan penyelesaian. Tanpa gerbang ini komplain bisa
 * ditandai selesai tanpa pernah diceklik siapa pun.
 */
class PatientComplaintService
{
    /**
     * Urutan status; indeks dipakai untuk memvalidasi lompatan tahap.
     *
     * @var array<string, int>
     */
    protected const URUTAN = [
        PatientComplaint::STATUS_BARU => 0,
        PatientComplaint::STATUS_DIPROSES => 1,
        PatientComplaint::STATUS_SELESAI => 2,
    ];

    /**
     * Catat komplain baru. Status selalu 'baru' apa pun isinya request -
     * penugasan dan penyelesaian hanya boleh lewat update() agar gerbang
     * prasyarat tidak bisa dilewati dari endpoint store.
     */
    public function create(array $data): PatientComplaint
    {
        return PatientComplaint::create($data + ['status' => PatientComplaint::STATUS_BARU]);
    }

    /**
     * Perbarui atribut komplain dengan gerbang transisi status.
     *
     * Aturan:
     * - status hanya boleh maju SATU tahap (tidak mundur, tidak lompat);
     * - naik ke 'diproses' wajib punya handled_by (bisa dari payload atau
     *   yang sudah tertanam);
     * - naik ke 'selesai' wajib punya resolution_notes;
     * - field non-status tetap boleh dikoreksi kapan pun, termasuk saat
     *   sudah selesai (mis. salah ketik deskripsi), karena bukan bagian
     *   jejak audit proses.
     */
    public function update(PatientComplaint $complaint, array $data): PatientComplaint
    {
        return DB::transaction(function () use ($complaint, $data) {
            // lockForUpdate supaya dua petugas yang memproses komplain yang sama
            // secara bersamaan tidak lolos gerbang bersama-sama (race condition).
            $complaint = PatientComplaint::query()->lockForUpdate()->findOrFail($complaint->id);

            if (array_key_exists('status', $data) && $data['status'] !== $complaint->status) {
                $target = $data['status'];

                abort_if(
                    ! array_key_exists($target, self::URUTAN)
                    || self::URUTAN[$target] !== self::URUTAN[$complaint->status] + 1,
                    422,
                    "Perubahan status dari '{$complaint->status}' ke '{$target}' tidak diizinkan - status hanya boleh maju bertahap: baru > diproses > selesai.",
                );

                if ($target === PatientComplaint::STATUS_DIPROSES) {
                    $handledBy = $data['handled_by'] ?? $complaint->handled_by;

                    abort_if(blank($handledBy), 422, 'Komplain harus ditugaskan ke petugas (handled_by) sebelum bisa diproses.');
                }

                if ($target === PatientComplaint::STATUS_SELESAI) {
                    $notes = $data['resolution_notes'] ?? $complaint->resolution_notes;

                    abort_if(blank($notes), 422, 'Komplain tidak bisa diselesaikan tanpa resolution_notes.');
                }
            }

            $complaint->update($data);

            return $complaint->refresh();
        });
    }

    /**
     * Hapus komplain. Komplain yang sudah selesai tidak boleh dihapus karena
     * menjadi bagian rekam keluhan pasien (audit mutu rumah sakit) - kalau
     * salah input, selesaikan dulu alurnya atau biarkan sebagai riwayat.
     */
    public function delete(PatientComplaint $complaint): void
    {
        abort_if(
            $complaint->status === PatientComplaint::STATUS_SELESAI,
            422,
            'Komplain berstatus selesai tidak boleh dihapus.',
        );

        $complaint->delete();
    }

    /**
     * Rekap jumlah komplain per status. Status yang tidak punya data tetap
     * muncul bernilai 0 supaya konsumen (dashboard) tidak perlu cek key.
     *
     * $status opsional: bila diisi (dari query param ?status=), hanya hitungan
     * status itu yang dikembalikan; nilai asing ditolak 422 agar typo seperti
     * "proses" tidak diam-diam dianggap "tidak ada data".
     *
     * @return array{baru: int, diproses: int, selesai: int}|array<string, int>
     */
    public function summaryCounts(?string $status = null): array
    {
        abort_if(
            $status !== null && ! array_key_exists($status, self::URUTAN),
            422,
            "Status '{$status}' tidak dikenal - pilih salah satu: baru, diproses, selesai.",
        );

        $counts = PatientComplaint::query()
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $semua = collect([
            PatientComplaint::STATUS_BARU,
            PatientComplaint::STATUS_DIPROSES,
            PatientComplaint::STATUS_SELESAI,
        ])
            ->mapWithKeys(fn (string $status): array => [$status => (int) $counts->get($status, 0)])
            ->all();

        return $status === null ? $semua : [$status => $semua[$status]];
    }
}
