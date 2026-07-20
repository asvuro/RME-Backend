<?php

namespace Modules\PendaftaranVisit\Services;

use App\Events\VisitDischarged;
use App\Events\VisitAdmitted;
use App\Events\VisitTransferred;
use App\Modules\Contracts\BedGate;
use App\Modules\Contracts\BillingGate;
use App\Modules\Contracts\HospitalConfig;
use App\Modules\Contracts\VisitGate;
use App\Modules\Contracts\WardScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\LayananPatientDischargeRecord\Models\PatientDischargeRecord;
use Modules\GeneralWard\Models\Ward;
use Modules\GeneralWardTariff\Models\WardTariff;
use Modules\PendaftaranVisit\Models\Visit;
use Modules\PendaftaranVisit\Models\VisitTransfer;

/**
 * Domain service kunjungan — port gerbang KunjunganResource::create() simgos2
 * plus gerbang mutasi/pulang (trigger onAfterUpdateKunjungan + onAfterUpdateMutasi).
 *
 * Urutan gerbang admit (ala aslinya):
 *   1. config admission.* via HospitalConfig (PropertiConfig 118/69)
 *   2. blokir bila pasien sudah pulang
 *   3. blokir bila tagihan terkunci kasir (BillingGate / config 69)
 *   4. deteksi kunjungan aktif ganda pasien yang sama
 *   5. okupansi bed atomik lewat BedGate (#11) bila admit membawa bed
 *   6. generate nomor + create dalam transaksi
 *   7. event VisitAdmitted (efek samping non-kritis; listener menyusul #12)
 */
class VisitService implements VisitGate
{
    public function __construct(
        protected HospitalConfig $config,
        protected BillingGate $billingGate,
        protected BedGate $bedGate,
        protected WardScope $wardScope,
    ) {}

    /** Gerbang least-privilege #3: petugas hanya boleh menulis di ward tempat dia ditugaskan. */
    protected function assertWardAccess(User $user, ?int $wardId): void
    {
        abort_if(
            ! $this->wardScope->canAccessWard($user, $wardId),
            403,
            'Anda tidak ditugaskan ke ward kunjungan ini.',
        );
    }

    /** Kontrak VisitGate: dipakai modul layanan sebelum posting tindakan/resep. */
    public function isPatientDischarged(int $visitId): bool
    {
        return Visit::query()
            ->whereKey($visitId)
            ->whereNotNull('discharged_at')
            ->exists();
    }

    public function isActive(int $visitId): bool
    {
        return Visit::query()
            ->whereKey($visitId)
            ->whereNull('discharged_at')
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    /**
     * @param  array<string, mixed>  $data  hasil validasi StoreVisitRequest
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException gerbang menolak (422)
     */
    public function admit(array $data, User $user): Visit
    {
        // Gerbang 1: konfigurasi RS bisa mematikan validasi tertentu.
        if ($this->config->get('admission.block_discharged_patient', true)) {
            $this->assertNotDischarged($data);
        }

        if ($this->config->get('billing.lock_on_cashier_close', true)) {
            $this->assertNotBillingLocked($data);
        }

        if ($this->config->get('admission.check_double_visit', true)) {
            $this->assertNoActiveDuplicate($data);
        }

        if ($this->config->get('admission.check_bed_availability', true)) {
            $this->assertBedConsistent($data);
        }

        $this->assertWardAccess($user, isset($data['ward_id']) ? (int) $data['ward_id'] : null);

        $visit = DB::transaction(function () use ($data, $user) {
            // Port trigger onAfterInsertKunjungan: bed yang dibawa kunjungan
            // langsung terisi; okupansi dicek atomik di sumber kebenaran bed.
            if (! empty($data['bed_id'])) {
                $this->bedGate->occupy((int) $data['bed_id']);
            }

            // 'status' TIDAK boleh datang dari input: kunjungan baru selalu
            // mulai dari default kolom ('active'). Kalau klien bisa suntik
            // status=discharged/cancelled saat admit, seluruh gerbang state
            // machine (bed release, cek tagihan, event audit) di cancel()/
            // discharge() bisa dilewati sejak awal.
            return Visit::create([
                ...Arr::except($data, 'status'),
                'visit_number' => $data['visit_number'] ?? Visit::generateVisitNumber(),
                'admitted_at' => $data['admitted_at'] ?? now(),
                'received_by' => $user->id,
            ]);
        });

        // Event di luar transaksi: listener hanya boleh menyentuh data yang
        // sudah commit (efek samping non-kritis; audit menyusul #12).
        VisitAdmitted::dispatch($visit);

        return $visit;
    }

    /**
     * Gerbang mutasi antar bed/ward — port pendaftaran.mutasi simgos2.
     *
     * Urutan ala trigger aslinya: bed tujuan ditempati lebih dulu (gagal = tak
     * ada efek), kunjungan pindah, riwayat dicatat, lalu bed lama dibebaskan
     * dengan cek tidak ada kunjungan aktif lain yang masih menunjuknya.
     *
     * @return VisitTransfer riwayat mutasi yang baru saja tercipta
     */
    public function transfer(Visit $visit, int $targetBedId, User $user, ?string $notes = null): VisitTransfer
    {
        abort_if($visit->discharged_at !== null, 422, 'Kunjungan sudah pulang; tidak dapat dimutasi.');
        abort_if($visit->status === 'cancelled', 422, 'Kunjungan sudah batal; tidak dapat dimutasi.');

        // Ward-scope (#3): staf ward asal ATAU ward tujuan boleh melakukan
        // mutasi (mengirim atau menerima pasien keduanya wajar).
        $targetWardIdForGate = Bed::query()->with('room')->find($targetBedId)?->room?->ward_id;
        abort_if(
            ! $this->wardScope->canAccessWard($user, $visit->ward_id)
                && ! $this->wardScope->canAccessWard($user, $targetWardIdForGate),
            403,
            'Anda tidak ditugaskan ke ward asal maupun ward tujuan mutasi ini.',
        );

        [$transfer, $oldBedId] = DB::transaction(function () use ($visit, $targetBedId, $user, $notes) {
            $targetBed = Bed::query()->lockForUpdate()->findOrFail($targetBedId);
            $targetWardId = (int) $targetBed->room->ward_id;

            abort_if(! $targetBed->is_active || $targetBed->status === Bed::STATUS_MAINTENANCE,
                422,
                "Bed tujuan #{$targetBedId} tidak dapat ditempati.",
            );

            abort_if($visit->bed_id === $targetBed->id, 422, 'Pasien sudah berada di bed tersebut.');

            $this->bedGate->occupy($targetBed->id);

            $oldWardId = $visit->ward_id;
            $oldBedId = $visit->bed_id;

            $visit->update(['ward_id' => $targetWardId, 'bed_id' => $targetBed->id]);

            $transfer = VisitTransfer::create([
                'visit_id' => $visit->id,
                'ward_from_id' => $oldWardId,
                'bed_from_id' => $oldBedId,
                'ward_to_id' => $targetWardId,
                'bed_to_id' => $targetBed->id,
                'transferred_by' => $user->id,
                'transferred_at' => now(),
                'notes' => $notes,
            ]);

            // Kunjungan sudah pindah: bed lama bebas bila memang tak ada lagi
            // kunjungan aktif lain di sana (cek di dalam release).
            if ($oldBedId !== null) {
                $this->bedGate->release((int) $oldBedId);
            }

            return [$transfer, $oldBedId];
        });

        VisitTransferred::dispatch($transfer);

        return $transfer;
    }

    /**
     * Gerbang pulang — port STATUS kunjungan → 2 pada onAfterUpdateKunjungan:
     * bed dibebaskan (dengan cek okupansi), rekam pulang tercipta, dan rawat
     * inap memposting akomodasi ala pembayaran.storeAkomodasi.
     *
     * @param  string|null  $dischargeMethod  cara pulang (modul referensi PenjaminRSDischargeMethod/GeneralDischargeCondition)
     */
    public function discharge(Visit $visit, string $finalOutcome, User $user, ?string $dischargeMethod = null, ?string $followUpNotes = null): Visit
    {
        abort_if($visit->discharged_at !== null, 422, 'Kunjungan sudah berstatus pulang.');
        abort_if($visit->status === 'cancelled', 422, 'Kunjungan sudah batal; tidak dapat dipulangkan.');
        abort_if(trim($finalOutcome) === '', 422, 'Hasil akhir kunjungan wajib diisi.');
        $this->assertWardAccess($user, $visit->ward_id);

        $dischargedAt = now();

        DB::transaction(function () use ($visit, $finalOutcome, $user, $dischargeMethod, $followUpNotes, $dischargedAt) {
            $visit->update([
                'discharged_at' => $dischargedAt,
                'status' => 'discharged',
                'final_outcome' => $finalOutcome,
                'final_outcome_by' => $user->id,
                'final_outcome_at' => $dischargedAt,
            ]);

            PatientDischargeRecord::create([
                'visit_id' => $visit->id,
                'patient_id' => $visit->registration->patient_id,
                'discharged_at' => $dischargedAt,
                'discharge_method' => $dischargeMethod,
                // discharged_by menunjuk employees; aktor user tercatat di final_outcome_by.
                'discharged_by' => null,
                'follow_up_notes' => $followUpNotes,
            ]);

            if ($visit->bed_id !== null) {
                $this->bedGate->release((int) $visit->bed_id);
            }
        });

        // Ala storeAkomodasi: hanya rawat inap (punya ward); tagihan yang sudah
        // dikunci kasir tidak disentuh — pelunasan jalur #9, bukan urusan pulang.
        if ($visit->ward_id !== null
            && $this->config->get('billing.auto_accommodation_on_discharge', true)
            && ! $this->billingGate->isVisitLocked($visit->id)) {
            $this->postAccommodation($visit, $dischargedAt);
        }

        $visit->refresh();

        VisitDischarged::dispatch($visit);

        return $visit;
    }

    /**
     * Batalkan kunjungan (soft-cancel) — bukan hard delete. Port semangat
     * status batal simgos2: bed dibebaskan bila terisi, tagihan yang sudah
     * dikunci kasir memblokir pembatalan, dan riwayat kunjungan tetap ada
     * untuk audit (status berubah jadi 'cancelled', bukan baris hilang).
     */
    public function cancel(Visit $visit, User $user): Visit
    {
        abort_if($visit->discharged_at !== null, 422, 'Kunjungan sudah pulang; tidak dapat dibatalkan.');
        abort_if($visit->status === 'cancelled', 422, 'Kunjungan sudah batal.');
        abort_if($this->billingGate->isVisitLocked($visit->id), 422, 'Tagihan kunjungan sudah dikunci kasir; tidak dapat dibatalkan.');
        $this->assertWardAccess($user, $visit->ward_id);

        DB::transaction(function () use ($visit) {
            $visit->update(['status' => 'cancelled']);

            if ($visit->bed_id !== null) {
                $this->bedGate->release((int) $visit->bed_id);
            }
        });

        return $visit->refresh();
    }

    /**
     * Sunting atribut non-gerbang kunjungan (mis. attending_physician_id,
     * is_deposit, deposit_class_id, visit_number, final_outcome) via
     * PUT/PATCH /visits/{visit}. ward_id/bed_id (gerbang #11 transfer()),
     * status/discharged_at (gerbang cancel()/discharge()) HARUS lewat
     * gerbang masing-masing dan tidak boleh lewat sini — controller sudah
     * menahan field tsb sebelum data mencapai method ini.
     *
     * @param  array<string, mixed>  $data  hasil validasi UpdateVisitRequest,
     *                                       sudah bersih dari ward_id/bed_id/status/discharged_at
     */
    public function updateDetails(Visit $visit, array $data, User $user): Visit
    {
        abort_if($visit->discharged_at !== null, 422, 'Kunjungan sudah pulang; tidak dapat disunting.');
        abort_if($visit->status === 'cancelled', 422, 'Kunjungan sudah batal; tidak dapat disunting.');
        $this->assertWardAccess($user, $visit->ward_id);

        DB::transaction(function () use ($visit, $data) {
            $visit->update($data);
        });

        return $visit->refresh();
    }

    /** Posting item "Akomodasi" dari masa rawat × tarif ward/kelas kamar. */
    protected function postAccommodation(Visit $visit, $dischargedAt): void
    {
        $tariff = WardTariff::query()
            ->where('ward_id', $visit->ward_id)
            ->when($visit->bed_id !== null && $visit->bed?->room?->class_id !== null,
                fn ($q) => $q->where(function ($qq) use ($visit) {
                    $qq->where('room_class_id', $visit->bed->room->class_id)
                        ->orWhereNull('room_class_id');
                }),
                fn ($q) => $q->whereNull('room_class_id'),
            )
            ->where('is_active', true)
            ->orderByRaw('room_class_id IS NULL')
            ->first();

        if ($tariff === null) {
            return; // tanpa tarif terpasang, tidak ada yang bisa diposting.
        }

        // Lama dirawat minimal 1 hari (ala getLamaDirawat).
        $nights = max(1, (int) ceil($visit->admitted_at->diffInHours($dischargedAt) / 24));

        $wardName = Ward::query()->whereKey($visit->ward_id)->value('name');

        $this->billingGate->postServiceItem(
            $visit->id,
            sprintf('Akomodasi %s (%d hari)', $wardName ?? 'rawat inap', $nights),
            'accommodation',
            $nights,
            (float) $tariff->price,
        );
    }

    /** Gerbang 2: registrasi terkait sudah pulang → tolak admit baru. */
    protected function assertNotDischarged(array $data): void
    {
        $discharged = Visit::query()
            ->where('registration_id', $data['registration_id'])
            ->whereNotNull('discharged_at')
            ->exists();

        abort_if($discharged, 422, 'Pasien sudah pulang untuk registrasi ini; tidak dapat admit kunjungan baru.');
    }

    /** Gerbang 3: tagihan kunjungan aktif dikunci kasir → tolak. */
    protected function assertNotBillingLocked(array $data): void
    {
        $locked = Visit::query()
            ->where('registration_id', $data['registration_id'])
            ->whereNull('discharged_at')
            ->pluck('id')
            ->contains(fn (int $visitId) => $this->billingGate->isVisitLocked($visitId));

        abort_if($locked, 422, 'Tagihan kunjungan aktif sudah dikunci oleh kasir; hubungi pembayaran.');
    }

    /** Gerbang 4: kunjungan aktif ganda pasien yang sama pada ward sama. */
    protected function assertNoActiveDuplicate(array $data): void
    {
        if (! array_key_exists('ward_id', $data)) {
            return;
        }

        // Cakupan ketat pasien yang sama: aktif DAN (ward sama ATAU bed sama).
        // Konflik bed lintas-pasien ditutup gerbang okupansi bed (#11).
        $duplicate = Visit::query()
            ->where('registration_id', $data['registration_id'])
            ->whereNull('discharged_at')
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($data) {
                $q->where('ward_id', $data['ward_id'])
                    ->when(isset($data['bed_id']), fn ($qq) => $qq->orWhere('bed_id', $data['bed_id']));
            })
            ->exists();

        abort_if($duplicate, 422, 'Kunjungan aktif ganda terdeteksi untuk pasien/ward ini.');
    }

    /**
     * Gerbang 5 (#11): konsistensi admit-ber-bed — bed harus milik ward yang
     * sama bila keduanya diisi. Okupansi lintas-pasien dicek atomik oleh
     * BedGate saat transaksi create.
     */
    protected function assertBedConsistent(array $data): void
    {
        if (empty($data['bed_id'])) {
            return;
        }

        $bed = Bed::query()->with('room')->find((int) $data['bed_id']);

        abort_if($bed === null, 422, "Bed #{$data['bed_id']} tidak dikenal.");

        if (array_key_exists('ward_id', $data) && ! empty($data['ward_id'])) {
            abort_if(
                (int) $bed->room->ward_id !== (int) $data['ward_id'],
                422,
                'Bed tidak berada di ward kunjungan yang dimaksud.',
            );
        }
    }
}
