<?php

namespace Modules\KemkesReport\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\GeneralRoomClass\Models\RoomClass;
use Modules\GeneralWard\Models\Ward;

/**
 * Port informasi.listKunjunganRIKemkes: rekap kunjungan rawat inap per kelas.
 */
class VisitsByClassTest extends KemkesReportTestCase
{
    use RefreshDatabase;

    public function test_rekap_per_kelas_termasuk_tanpa_kelas(): void
    {
        $kelas1 = RoomClass::query()->create(['name' => 'Kelas 1']);
        $kelas3 = RoomClass::query()->create(['name' => 'Kelas 3']);
        $ward = Ward::factory()->create();

        $roomKelas1 = $this->makeRoom($ward, $kelas1->id);
        $roomKelas3 = $this->makeRoom($ward, $kelas3->id);
        $roomTanpa = $this->makeRoom($ward, null);
        [$bed1] = $this->makeBeds($roomKelas1, 1);
        [$bed3a, $bed3b] = $this->makeBeds($roomKelas3, 2);

        $this->makeInpatientVisit($ward, $bed1, '2026-08-24 09:00:00');
        $this->makeInpatientVisit($ward, $bed3a, '2026-08-24 10:00:00');
        $this->makeInpatientVisit($ward, $bed3b, '2026-08-25 11:00:00');
        // Kunjungan rawat jalan (tanpa ward) tidak masuk rekap RI.
        $this->makeInpatientVisit($ward, null, '2026-08-25 12:00:00');

        $data = $this->getJson('/api/v1/kemkes-reports/inpatient-visits-by-class?from=2026-08-24&to=2026-08-25')
            ->assertOk()
            ->json('data');

        $rows = collect($data['rows'])->keyBy('class_name');
        $this->assertSame(1, $rows['Kelas 1']['visits']);
        $this->assertSame(2, $rows['Kelas 3']['visits']);
        $this->assertSame(1, $rows['Tanpa Kelas']['visits']);
    }

    public function test_hanya_kunjungan_dalam_rentang(): void
    {
        $ward = Ward::factory()->create();
        $room = $this->makeRoom($ward);
        [$bedA] = $this->makeBeds($room, 1);

        $this->makeInpatientVisit($ward, $bedA, '2026-08-20 08:00:00');
        $this->makeInpatientVisit($ward, null, '2026-08-25 08:00:00');

        $data = $this->getJson('/api/v1/kemkes-reports/inpatient-visits-by-class?from=2026-08-25')
            ->assertOk()
            ->json('data');

        $this->assertSame(1, collect($data['rows'])->sum('visits'));
    }
}
