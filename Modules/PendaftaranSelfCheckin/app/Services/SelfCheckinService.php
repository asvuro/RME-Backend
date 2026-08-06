<?php

namespace Modules\PendaftaranSelfCheckin\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\PendaftaranSelfCheckin\Models\SelfCheckinQueue;

/**
 * Domain service antrian self-check-in Anjungan Pasien Mandiri (kiosk).
 *
 * State machine sengaja linear dan ketat:
 *
 *   waiting --call--> called --complete--> completed
 *      \--- (no_show: belum ada endpoint, disiapkan utk penandaan manual) ---/
 *
 * Semua perpindahan status HANYA boleh lewat method di sini — controller tidak
 * pernah menulis model langsung, agar gerbang urutan (complete tanpa call =
 * ditolak) dan deduplikasi antrian ganda tidak bisa dilewati dari luar.
 */
class SelfCheckinService
{
    /**
     * Check-in pasien dari kiosk.
     *
     * Asumsi auth kiosk: endpoint ini dipanggil device anjungan memakai token
     * SERVICE ACCOUNT milik RS (bukan token pasien), sehingga tidak ada
     * parameter user aktor di sini — identitas manusia baru muncul saat
     * panggil/complete oleh petugas loket (called_by).
     *
     * @param  array<string, mixed>  $data  hasil validasi StoreSelfCheckinQueueRequest
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException gerbang menolak (422)
     */
    public function checkIn(array $data): SelfCheckinQueue
    {
        // Normalisasi: kiosk boleh mengirim payload tanpa kunci sama sekali
        // (ward_id dihilangkan = anjungan umum), gerbang tetap harus jalan.
        $patientId = filled($data['patient_id'] ?? null) ? (int) $data['patient_id'] : null;
        $nik = filled($data['nik'] ?? null) ? trim((string) $data['nik']) : null;
        $wardId = filled($data['ward_id'] ?? null) ? (int) $data['ward_id'] : null;

        // Gerbang identitas minimum: kiosk wajib membawa minimal satu penanda
        // pasien. Tanpa ini antrian yatim tanpa NIK/pasien menumpuk tanpa cara
        // didebug (FormRequest sudah menolak, ini pertahanan kedua).
        abort_if(
            $patientId === null && $nik === null,
            422,
            'Identitas wajib: pilih pasien terdaftar atau isi NIK.',
        );

        $this->assertNoActiveDuplicate($patientId, $nik, $wardId);

        // 'status' TIDAK boleh datang dari input: antrian baru selalu mulai
        // 'waiting'. Kalau kiosk bisa menyuntik status=called/completed,
        // seluruh state machine call()/complete() jadi bisa dilewati.
        return DB::transaction(function () use ($patientId, $nik, $wardId) {
            $checkedInAt = now();

            return SelfCheckinQueue::create([
                'patient_id' => $patientId,
                'nik' => $nik,
                'ward_id' => $wardId,
                'queue_number' => $this->nextQueueNumber($wardId, $checkedInAt->toDateString()),
                'queue_date' => $checkedInAt->toDateString(),
                'checked_in_at' => $checkedInAt,
                'status' => SelfCheckinQueue::STATUS_WAITING,
            ]);
        });
    }

    /**
     * Petugas loket memanggil nomor antrian.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 422 bila bukan waiting
     */
    public function call(SelfCheckinQueue $queue, User $user): SelfCheckinQueue
    {
        abort_if($queue->status !== SelfCheckinQueue::STATUS_WAITING, 422, 'Antrian sudah dipanggil atau sudah selesai.');

        DB::transaction(function () use ($queue, $user): void {
            $queue->update([
                'status' => SelfCheckinQueue::STATUS_CALLED,
                'called_at' => now(),
                // Aktor pencatatan: petugas loket yang menekan "panggil",
                // bukan device kiosk yang melakukan check-in.
                'called_by' => $user->id,
            ]);
        });

        return $queue->refresh();
    }

    /**
     * Petugas loket menyelesaikan/melanjutkan pasien yang sudah dipanggil.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 422 bila belum pernah dipanggil
     */
    public function complete(SelfCheckinQueue $queue, User $user): SelfCheckinQueue
    {
        // Urutan ketat: complete hanya valid setelah call. Kalau complete bisa
        // dari waiting, waktu tunggu (checked_in_at -> called_at) jadi bisa
        // nol dan laporan layanan antrian tidak bisa dipercaya.
        abort_if($queue->status === SelfCheckinQueue::STATUS_WAITING, 422, 'Antrian belum dipanggil; panggil dulu sebelum diselesaikan.');
        abort_if($queue->status !== SelfCheckinQueue::STATUS_CALLED, 422, 'Antrian sudah selesai diproses.');

        DB::transaction(function () use ($queue): void {
            $queue->update(['status' => SelfCheckinQueue::STATUS_COMPLETED]);
        });

        return $queue->refresh();
    }

    /**
     * Gerbang anti-antrian ganda: pasien yang sama (patient_id ATAU NIK sama)
     * masih punya antrian aktif (waiting/called) di poli yang sama pada hari
     * yang sama → tolak. Mencegah spam tombol di kiosk membuat belasan nomor
     * untuk satu orang dan merusak keadilan urutan panggil.
     *
     * ward_id NULL diperlakukan sebagai bucket "anjungan umum": dua check-in
     * tanpa poli untuk pasien yang sama juga dianggap duplikat. Pembanding
     * null harus eksplisit karena where('ward_id', null) (=NULL) tidak pernah
     * match baris NULL di SQL.
     */
    protected function assertNoActiveDuplicate(?int $patientId, ?string $nik, ?int $wardId): void
    {
        if ($patientId === null && $nik === null) {
            return;
        }

        $duplicate = SelfCheckinQueue::query()
            ->whereIn('status', [SelfCheckinQueue::STATUS_WAITING, SelfCheckinQueue::STATUS_CALLED])
            ->whereDate('queue_date', now()->toDateString())
            ->where(function ($q) use ($wardId) {
                $wardId === null ? $q->whereNull('ward_id') : $q->where('ward_id', $wardId);
            })
            ->where(function ($q) use ($patientId, $nik) {
                $q->when($patientId !== null, fn ($qq) => $qq->orWhere('patient_id', $patientId))
                    ->when($nik !== null, fn ($qq) => $qq->orWhere('nik', $nik));
            })
            ->exists();

        abort_if($duplicate, 422, 'Pasien ini masih memiliki antrian aktif di poli tersebut hari ini.');
    }

    /**
     * Nomor urut harian per poli: max(queue_number) hari itu + 1, format %03d.
     *
     * Keterbatasan yang sama dengan Patient::generateMedicalRecordNumber() /
     * Visit::generateVisitNumber(): read-then-write, tidak aman terhadap dua
     * check-in benar-benar bersamaan. Unique index (ward_id, queue_date,
     * queue_number) menjadi pengaman terakhir — duplikat gagal insert alih-
     * alih menghasilkan dua nomor kembar. Revisit dengan lock/sequence bila
     * volume kiosk nyata menjadi masalah.
     */
    protected function nextQueueNumber(?int $wardId, string $date): string
    {
        $max = SelfCheckinQueue::query()
            ->where('ward_id', $wardId)
            ->whereDate('queue_date', $date)
            ->max('queue_number');

        return sprintf('%03d', ((int) $max) + 1);
    }
}
