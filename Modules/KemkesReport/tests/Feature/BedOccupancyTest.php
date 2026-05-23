<?php

namespace Modules\KemkesReport\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralWard\Models\Ward;

/**
 * Port informasi.executeBedMonitorKemkes: okupansi dari kunjungan yang
 * menginap pada tanggal laporan — bukan status live bed.
 */
class BedOccupancyTest extends KemkesReportTestCase
{
    use RefreshDatabase;

    public function test_okupansi_dihitung_per_bangsal_dari_kunjungan_menginap(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);
        [$bedA, $bedB, $bedC] = $this->makeBeds($room, 3);

        $laki = $this->makePatient($this->male);
        $perempuan = $this->makePatient($this->female);
        $this->makeInpatientVisit($ward, $bedA, now()->subDay()->toDateTimeString(), null, $laki);
        $this->makeInpatientVisit($ward, $bedB, now()->subHours(5)->toDateTimeString(), null, $perempuan);

        $response = $this->getJson('/api/v1/kemkes-reports/bed-occupancy');

        $response->assertOk();
        $row = collect($response->json('data.rows'))->firstWhere('ward_id', $ward->id);
        $this->assertSame(3, $row['total_beds']);
        $this->assertSame(2, $row['occupied']);
        $this->assertSame(1, $row['available']);
        $this->assertSame(1, $row['male_patients']);
        $this->assertSame(1, $row['female_patients']);
        $this->assertEqualsWithDelta(66.7, $row['occupancy_rate'], 0.01);

        $totals = $response->json('data.totals');
        $this->assertSame(3, $totals['total_beds']);
        $this->assertSame(2, $totals['occupied']);
    }

    public function test_bed_maintenance_dan_nonaktif_tidak_masuk_total(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);
        [$bedA, $bedB] = $this->makeBeds($room, 2);
        $bedB->update(['status' => Bed::STATUS_MAINTENANCE]);

        Bed::factory()->create(['room_id' => $room->id, 'is_active' => false]);

        $this->makeInpatientVisit($ward, $bedA, now()->subDay()->toDateTimeString());

        $row = collect($this->getJson('/api/v1/kemkes-reports/bed-occupancy')->json('data.rows'))
            ->firstWhere('ward_id', $ward->id);

        // Hanya bedA yang terhitung — bed perbaikan dan bed nonaktif tereliminasi.
        $this->assertSame(1, $row['total_beds']);
        $this->assertSame(1, $row['occupied']);
    }

    public function test_laporan_tanggal_lampau_mengabaikan_perubahan_setelahnya(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);
        [$bedA, $bedB] = $this->makeBeds($room, 2);

        // Menginap dua hari lalu, pulang kemarin — TIDAK dihitung hari ini.
        $this->makeInpatientVisit(
            $ward,
            $bedA,
            now()->subDays(3)->toDateTimeString(),
            now()->subDay()->toDateTimeString(),
        );
        // Masuk hari ini — belum menginap kemarin.
        $this->makeInpatientVisit($ward, $bedB, now()->toDateTimeString());

        $kemarin = now()->subDay()->toDateString();
        $row = collect(
            $this->getJson("/api/v1/kemkes-reports/bed-occupancy?date={$kemarin}")->json('data.rows')
        )->firstWhere('ward_id', $ward->id);

        $this->assertSame(0, $row['occupied']);
        $this->assertSame(2, $row['available']);
    }

    public function test_endpoint_tertutup_untuk_tamu(): void
    {
        $this->app['auth']->guard('sanctum')->forgetUser();

        $this->getJson('/api/v1/kemkes-reports/bed-occupancy')->assertUnauthorized();
    }
}
