<?php

namespace Modules\KemkesReport\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralRoom\Models\Room;
use Modules\GeneralWard\Models\Ward;
use Modules\LayananPatientDischargeRecord\Models\PatientDischargeRecord;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranReferral\Models\Referral;
use Modules\PendaftaranVisit\Models\Visit;
use Modules\PendaftaranVisit\Models\VisitTransfer;

/**
 * Port informasi.executeIndikatorRS: indikator harian rawat inap + rasio
 * BOR/TOI/AVLOS/BTO/GDR/NDR.
 */
class InpatientIndicatorsTest extends KemkesReportTestCase
{
    use RefreshDatabase;

    public function test_indikator_harian_dasar(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);
        [$bedA] = $this->makeBeds($room, 1);
        $kemarin = now()->subDay();
        $hariIni = now();

        // Masuk kemarin, masih dirawat → awal & sisa hari ini.
        $this->makeInpatientVisit($ward, $bedA, $kemarin->toDateTimeString());
        // Masuk hari ini.
        $this->makeInpatientVisit($ward, null, $hariIni->toDateTimeString());

        $data = $this->getJson('/api/v1/kemkes-reports/inpatient-indicators?from='.$hariIni->toDateString())
            ->assertOk()
            ->json('data');

        $day = $data['days'][0];
        $this->assertSame(1, $day['awal']);
        $this->assertSame(1, $day['masuk']);
        // Sisa = pasien akhir hari — termasuk yang baru masuk pagi itu (padan union
        // terakhir executeIndikatorRS: MASUK < TGL+1 dan belum pulang).
        $this->assertSame(2, $day['sisa']);
        $this->assertSame(2, $day['hari_perawatan']);
        $this->assertSame(0, $day['keluar']);
        $this->assertSame(1, $data['summary']['total_beds']);
    }

    public function test_keluar_dan_lama_dirawat_hari_ini(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);

        // Masuk 22 Agu 09:00, pulang 25 Agu 14:00 → LD = 3 hari.
        $this->makeInpatientVisit(
            $ward,
            null,
            '2026-08-22 09:00:00',
            '2026-08-25 14:00:00',
        );

        $day = $this->getJson('/api/v1/kemkes-reports/inpatient-indicators?from=2026-08-25')
            ->assertOk()
            ->json('data.days.0');

        $this->assertSame(1, $day['keluar']);
        $this->assertSame(3, $day['lama_dirawat']);
    }

    public function test_mati_kurang_dan_lebih_48_jam(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);

        // Meninggal 20 jam setelah masuk → kurang 48 jam.
        $cepat = $this->makeInpatientVisit($ward, null, '2026-08-24 10:00:00', '2026-08-25 06:00:00');
        PatientDischargeRecord::query()->create([
            'visit_id' => $cepat->id,
            'patient_id' => $cepat->registration->patient_id,
            'discharged_at' => '2026-08-25 06:00:00',
            'discharge_method' => 'died',
        ]);

        // Meninggal 80 jam setelah masuk → lebih 48 jam.
        $lambat = $this->makeInpatientVisit($ward, null, '2026-08-21 22:00:00', '2026-08-25 06:00:00');
        PatientDischargeRecord::query()->create([
            'visit_id' => $lambat->id,
            'patient_id' => $lambat->registration->patient_id,
            'discharged_at' => '2026-08-25 06:00:00',
            'discharge_method' => 'died',
        ]);

        $day = $this->getJson('/api/v1/kemkes-reports/inpatient-indicators?from=2026-08-25')
            ->assertOk()
            ->json('data.days.0');

        $this->assertSame(2, $day['keluar']);
        $this->assertSame(1, $day['mati_kurang_48jam']);
        $this->assertSame(1, $day['mati_lebih_48jam']);
    }

    public function test_mutasi_antar_unit_terhitung_sebagai_dipindahkan(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);
        [$bedA] = $this->makeBeds($room, 1);
        $visit = $this->makeInpatientVisit($ward, $bedA, now()->subDay()->toDateTimeString());

        VisitTransfer::query()->create([
            'visit_id' => $visit->id,
            'ward_from_id' => $ward->id,
            'bed_from_id' => $bedA->id,
            'ward_to_id' => $ward->id,
            'bed_to_id' => $bedA->id,
            'transferred_by' => $this->user->id,
            'transferred_at' => now(),
        ]);

        $day = $this->getJson('/api/v1/kemkes-reports/inpatient-indicators?from='.now()->toDateString())
            ->assertOk()
            ->json('data.days.0');

        $this->assertSame(1, $day['dipindahkan']);
    }

    public function test_rujukan_masuk_dari_rs_lain_terhitung_sebagai_pindahan(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);

        $referral = Referral::factory()->create(['direction' => 'incoming']);
        $registration = Registration::factory()->create([
            'patient_id' => $this->makePatient($this->male)->id,
            'referral_id' => $referral->id,
        ]);
        $visitWithReferral = Visit::factory()->create([
            'registration_id' => $registration->id,
            'ward_id' => $ward->id,
            'admitted_at' => now(),
        ]);

        // Kunjungan tanpa rujukan pada hari yang sama tidak boleh ikut terhitung.
        $this->makeInpatientVisit($ward, null, now()->toDateTimeString());

        $day = $this->getJson('/api/v1/kemkes-reports/inpatient-indicators?from='.now()->toDateString())
            ->assertOk()
            ->json('data.days.0');

        $this->assertSame(1, $day['pindahan']);
        $this->assertSame(2, $day['masuk']);
    }

    public function test_rujukan_keluar_tidak_terhitung_sebagai_pindahan(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);

        $referral = Referral::factory()->create(['direction' => 'outgoing']);
        $registration = Registration::factory()->create([
            'patient_id' => $this->makePatient($this->male)->id,
            'referral_id' => $referral->id,
        ]);
        Visit::factory()->create([
            'registration_id' => $registration->id,
            'ward_id' => $ward->id,
            'admitted_at' => now(),
        ]);

        $day = $this->getJson('/api/v1/kemkes-reports/inpatient-indicators?from='.now()->toDateString())
            ->assertOk()
            ->json('data.days.0');

        $this->assertSame(0, $day['pindahan']);
    }

    public function test_rasio_rentang_tiga_hari(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);
        $this->makeBeds($room, 2);

        // Satu pasien masuk 22 Agu, pulang 25 Agu → LD=3 pada rentang 23–25.
        $this->makeInpatientVisit($ward, null, '2026-08-22 08:00:00', '2026-08-25 10:00:00');

        $data = $this->getJson('/api/v1/kemkes-reports/inpatient-indicators?from=2026-08-23&to=2026-08-25')
            ->assertOk()
            ->json('data');

        $this->assertCount(3, $data['days']);
        $summary = $data['summary'];
        $this->assertSame(2, $summary['total_beds']);
        $this->assertSame(3, $summary['lama_dirawat']);
        $this->assertSame(1, $summary['keluar']);

        // bedDays = 2 bed × 3 hari = 6 → BOR = 3/6 = 50%; AVLOS = 3/1 = 3.
        $this->assertEqualsWithDelta(50.0, $summary['bor_percent'], 0.01);
        $this->assertEqualsWithDelta(3.0, $summary['avlos_days'], 0.01);
        // TOI = (6-3)/1 = 3; NDR = 0/LD×1000 = 0.
        $this->assertEqualsWithDelta(3.0, $summary['toi_days'], 0.01);
        $this->assertEqualsWithDelta(0.0, $summary['ndr_per_mille'], 0.01);
    }

    public function test_rentang_tanggal_terbalik_ditolak(): void
    {
        $this->getJson('/api/v1/kemkes-reports/inpatient-indicators?from=2026-08-25&to=2026-08-23')
            ->assertStatus(422);
    }
}
