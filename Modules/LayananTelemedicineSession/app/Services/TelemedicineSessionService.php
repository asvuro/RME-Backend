<?php

namespace Modules\LayananTelemedicineSession\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\LayananTelemedicineSession\Models\TelemedicineSession;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Domain service sesi telemedicine — gerbang urutan status
 * scheduled -> ongoing -> completed (cabang cancelled).
 *
 * Semua mutasi lewat sini; controller TIDAK pernah menulis Model::create()/
 * update() langsung supaya mesin status tidak bisa dilewati dari HTTP.
 */
class TelemedicineSessionService
{
    /**
     * Jadwalkan sesi telemedicine baru untuk sebuah kunjungan.
     *
     * Asumsi desain:
     * - status TIDAK pernah berasal dari input klien: sesi baru selalu
     *   'scheduled' sehingga start()/complete() tidak dapat dilewati sejak awal
     *   (pola yang sama dengan VisitService::admit).
     * - session_url diisi placeholder path lokal karena integrasi video call
     *   asli belum ada; klien boleh menimpa bila sudah memegang link lain.
     * - satu kunjungan cukup punya SATU sesi terjadwal/berjalan; sesi lama yang
     *   sudah completed/cancelled tidak menghalangi penjadwalan ulang.
     *
     * @param  array<string, mixed>  $data  hasil validasi StoreTelemedicineSessionRequest
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException gerbang menolak (422)
     */
    public function schedule(array $data): TelemedicineSession
    {
        $visit = Visit::query()->findOrFail($data['visit_id']);

        abort_if($visit->discharged_at !== null, 422, 'Kunjungan sudah pulang; tidak dapat menjadwalkan sesi telemedicine.');
        abort_if($visit->status === 'cancelled', 422, 'Kunjungan sudah batal; tidak dapat menjadwalkan sesi telemedicine.');

        $activeExists = TelemedicineSession::query()
            ->where('visit_id', $visit->id)
            ->whereIn('status', [TelemedicineSession::STATUS_SCHEDULED, TelemedicineSession::STATUS_ONGOING])
            ->exists();

        abort_if($activeExists, 422, 'Kunjungan ini masih punya sesi telemedicine yang terjadwal atau sedang berjalan.');

        return DB::transaction(function () use ($data) {
            return TelemedicineSession::create([
                ...$data,
                'session_url' => $data['session_url'] ?? sprintf('/telemedicine/rooms/%s', (string) Str::uuid()),
                'status' => TelemedicineSession::STATUS_SCHEDULED,
            ]);
        });
    }

    /**
     * Gerbang start: hanya sesi berstatus scheduled yang boleh dimulai, dan
     * kunjungannya harus masih hidup (tidak pulang/batal) saat mulai.
     */
    public function start(TelemedicineSession $session): TelemedicineSession
    {
        abort_if($session->status !== TelemedicineSession::STATUS_SCHEDULED, 422, 'Hanya sesi berstatus scheduled yang dapat dimulai.');

        $visit = $session->visit()->firstOrFail();
        abort_if(
            $visit->discharged_at !== null || $visit->status === 'cancelled',
            422,
            'Kunjungan sudah pulang/batal; sesi tidak dapat dimulai.',
        );

        DB::transaction(function () use ($session) {
            $session->update([
                'status' => TelemedicineSession::STATUS_ONGOING,
                'started_at' => now(),
            ]);
        });

        return $session->refresh();
    }

    /**
     * Gerbang complete: hanya sesi ongoing yang boleh diselesaikan. Ini
     * menegakkan urutan wajib start -> complete; catatan konsultasi akhir
     * ikut ditampung di momong penyelesaian.
     */
    public function complete(TelemedicineSession $session, ?string $notes = null): TelemedicineSession
    {
        abort_if($session->status !== TelemedicineSession::STATUS_ONGOING, 422, 'Sesi belum dimulai; hanya sesi berstatus ongoing yang dapat diselesaikan.');

        DB::transaction(function () use ($session, $notes) {
            $session->update([
                'status' => TelemedicineSession::STATUS_COMPLETED,
                'ended_at' => now(),
                // Tanpa catatan baru: pertahankan isi lama (mis. diisi via update).
                'consultation_notes' => $notes ?? $session->consultation_notes,
            ]);
        });

        return $session->refresh();
    }

    /**
     * Pembatalan lewat PUT {status=cancelled} (pola modul farmasi). Dilarang
     * dari completed/cancelled agar rekam jejak klinis tetap konsisten:
     * sesi yang sudah selesai tidak bisa "dipanggang ulang" jadi batal.
     */
    public function cancel(TelemedicineSession $session): TelemedicineSession
    {
        abort_if(
            in_array($session->status, [TelemedicineSession::STATUS_COMPLETED, TelemedicineSession::STATUS_CANCELLED], true),
            422,
            "Sesi sudah {$session->status}; tidak dapat dibatalkan.",
        );

        DB::transaction(fn () => $session->update(['status' => TelemedicineSession::STATUS_CANCELLED]));

        return $session->refresh();
    }

    /**
     * Sunting atribut non-gerbang sesi (jadwal, dokter, url, catatan).
     * Sesi completed/cancelled dikunci karena isinya sudah bagian rekam medis;
     * perubahan status TIDAK lewat sini (hanya cancel/start/complete).
     *
     * @param  array<string, mixed>  $data  hasil validasi UpdateTelemedicineSessionRequest tanpa 'status'
     */
    public function updateDetails(TelemedicineSession $session, array $data): TelemedicineSession
    {
        abort_if(
            in_array($session->status, [TelemedicineSession::STATUS_COMPLETED, TelemedicineSession::STATUS_CANCELLED], true),
            422,
            'Sesi sudah selesai/batal dan tidak dapat disunting.',
        );

        DB::transaction(fn () => $session->update($data));

        return $session->refresh();
    }

    /**
     * Hard delete hanya untuk sesi yang belum pernah hidup. Begitu status
     * meninggalkan scheduled, baris adalah jejak klinis: hanya boleh dibatalkan,
     * bukan dihapus dari database.
     */
    public function delete(TelemedicineSession $session): void
    {
        abort_if($session->status !== TelemedicineSession::STATUS_SCHEDULED, 422, 'Hanya sesi yang masih terjadwal yang dapat dihapus.');

        DB::transaction(fn () => $session->delete());
    }
}
