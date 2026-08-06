<?php

namespace Modules\MedicalRecordRetentionSchedule\Services;

use App\Modules\Contracts\HospitalConfig;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\MedicalRecordRetentionSchedule\Models\RetentionSchedule;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Gerbang bisnis retensi rekam medis (Permenkes 24/2022): satu baris
 * retention_schedules per registrasi, dihitung sekali saat dibuat lalu
 * ditinjau ulang statusnya oleh scan() terjadwal. Tidak ada operasi yang
 * benar-benar menghapus data pasien/kunjungan — "destroy" di modul ini
 * hanya menandai status.
 */
class RetentionScheduleService
{
    public function __construct(protected HospitalConfig $config) {}

    /**
     * Isi baris retention_schedules untuk semua registrasi yang belum
     * punya, lalu tandai baris 'active' yang sudah lewat retention_due_at
     * sebagai 'eligible_for_destruction'. Idempoten — aman dijalankan
     * berulang (mis. via scheduler harian).
     *
     * @return array{created: int, marked_eligible: int}
     */
    public function scan(): array
    {
        $created = $this->createMissingSchedules();
        $markedEligible = $this->markOverdueAsEligible();

        return ['created' => $created, 'marked_eligible' => $markedEligible];
    }

    protected function createMissingSchedules(): int
    {
        $years = (int) $this->config->get('retention.years', 25);
        $created = 0;

        // Registration (modul lain) sengaja tidak diberi relasi balik ke
        // sini — dicek lewat whereNotExists ke tabel retention_schedules
        // langsung agar modul ini tidak menyentuh model Registration.
        Registration::query()
            ->select(['id', 'patient_id', 'updated_at'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('retention_schedules')
                    ->whereColumn('retention_schedules.registration_id', 'registrations.id');
            })
            ->chunkById(200, function ($registrations) use ($years, &$created) {
                foreach ($registrations as $registration) {
                    // Cek ulang di dalam chunk: relasi bisa saja dibuat oleh
                    // proses lain di antara query dan iterasi ini.
                    if (RetentionSchedule::query()->where('registration_id', $registration->id)->exists()) {
                        continue;
                    }

                    DB::transaction(function () use ($registration, $years) {
                        $basisDate = $this->resolveBasisDate($registration);

                        RetentionSchedule::create([
                            'registration_id' => $registration->id,
                            'patient_id' => $registration->patient_id,
                            'basis_date' => $basisDate,
                            'retention_years' => $years,
                            'retention_due_at' => $basisDate->copy()->addYears($years)->toDateString(),
                            'status' => RetentionSchedule::STATUS_ACTIVE,
                        ]);
                    });

                    $created++;
                }
            });

        return $created;
    }

    /**
     * Basis perhitungan retensi: discharged_at kunjungan TERAKHIR milik
     * registrasi ini bila sudah pulang; bila belum ada kunjungan yang
     * pulang, pakai "aktivitas terakhir" — updated_at kunjungan terbaru,
     * atau updated_at registrasi itu sendiri bila belum ada kunjungan
     * sama sekali. Ini asumsi desain: proyek belum punya jejak aktivitas
     * terpusat per registrasi, jadi updated_at dipakai sebagai proksi.
     */
    protected function resolveBasisDate(Registration $registration): \Illuminate\Support\Carbon
    {
        $lastVisit = Visit::query()
            ->where('registration_id', $registration->id)
            ->latest('id')
            ->first();

        if ($lastVisit !== null && $lastVisit->discharged_at !== null) {
            return $lastVisit->discharged_at->copy();
        }

        if ($lastVisit !== null) {
            return $lastVisit->updated_at->copy();
        }

        return $registration->updated_at->copy();
    }

    protected function markOverdueAsEligible(): int
    {
        return RetentionSchedule::query()
            ->where('status', RetentionSchedule::STATUS_ACTIVE)
            ->whereDate('retention_due_at', '<=', now()->toDateString())
            ->update(['status' => RetentionSchedule::STATUS_ELIGIBLE_FOR_DESTRUCTION]);
    }

    /**
     * Tandai baris sebagai destroyed. HANYA mengubah status/marked_by/
     * marked_at — tidak pernah menghapus baris ini atau data pasien mana
     * pun. Ditolak bila belum eligible_for_destruction, mencegah
     * penandaan berkas yang masih wajib disimpan.
     */
    public function markDestroyed(RetentionSchedule $schedule, User $user, ?string $notes = null): RetentionSchedule
    {
        return DB::transaction(function () use ($schedule, $user, $notes) {
            $schedule = RetentionSchedule::query()->lockForUpdate()->findOrFail($schedule->id);

            abort_if(
                $schedule->status !== RetentionSchedule::STATUS_ELIGIBLE_FOR_DESTRUCTION,
                422,
                "Jadwal retensi #{$schedule->id} belum eligible_for_destruction.",
            );

            $schedule->update([
                'status' => RetentionSchedule::STATUS_DESTROYED,
                'marked_by' => $user->id,
                'marked_at' => now(),
                'notes' => $notes,
            ]);

            return $schedule;
        });
    }
}
