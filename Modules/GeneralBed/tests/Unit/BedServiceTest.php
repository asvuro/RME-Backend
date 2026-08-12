<?php

namespace Modules\GeneralBed\Tests\Unit;

use App\Modules\Contracts\BedGate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralBed\Services\BedService;
use Modules\PendaftaranVisit\Models\Visit;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Port state machine master.ruang_kamar_tidur simgos2 (referensi jenis 20):
 * kosong / dipesan / terisi / tidak-aktif, dengan cek okupansi ala trigger
 * onAfterUpdateKunjungan.
 */
class BedServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BedService $service;

    protected Bed $bed;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(BedService::class);
        $this->bed = Bed::factory()->create();
    }

    public function test_service_terikat_sebagai_bed_gate(): void
    {
        $this->assertInstanceOf(BedService::class, app(BedGate::class));
    }

    public function test_occupy_mengubah_kosong_menjadi_terisi(): void
    {
        $this->service->occupy($this->bed->id);

        $this->assertSame(Bed::STATUS_OCCUPIED, $this->bed->refresh()->status);
    }

    public function test_occupy_bed_sudah_terisi_ditolak_422(): void
    {
        $this->bed->update(['status' => Bed::STATUS_OCCUPIED]);

        try {
            $this->service->occupy($this->bed->id);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }

        $this->assertSame(Bed::STATUS_OCCUPIED, $this->bed->refresh()->status);
    }

    public function test_occupy_bed_perbaikan_ditolak_422(): void
    {
        $this->bed->update(['status' => Bed::STATUS_MAINTENANCE]);

        $this->assertThrows(
            fn () => $this->service->occupy($this->bed->id),
            HttpException::class,
        );
    }

    public function test_occupy_bed_nonaktif_ditolak_422(): void
    {
        $this->bed->update(['is_active' => false]);

        try {
            $this->service->occupy($this->bed->id);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }
    }

    public function test_release_membebaskan_bed_terisi(): void
    {
        $this->bed->update(['status' => Bed::STATUS_OCCUPIED]);

        $this->service->release($this->bed->id);

        $this->assertSame(Bed::STATUS_AVAILABLE, $this->bed->refresh()->status);
    }

    public function test_release_bed_kosong_idempoten(): void
    {
        $this->service->release($this->bed->id);

        $this->assertSame(Bed::STATUS_AVAILABLE, $this->bed->refresh()->status);
    }

    public function test_release_ditolak_bila_kunjungan_aktif_lain_masih_menunjuk(): void
    {
        // Port cek trigger onAfterUpdateKunjungan: jangan bebaskan bila masih
        // ada kunjungan aktif lain di bed itu.
        Visit::factory()->create(['bed_id' => $this->bed->id]);
        $this->bed->update(['status' => Bed::STATUS_OCCUPIED]);

        try {
            $this->service->release($this->bed->id);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }

        $this->assertSame(Bed::STATUS_OCCUPIED, $this->bed->refresh()->status);
    }

    public function test_release_tetap_bebas_bila_kunjungan_penunjuk_sudah_pulang(): void
    {
        Visit::factory()->discharged()->create(['bed_id' => $this->bed->id]);
        $this->bed->update(['status' => Bed::STATUS_OCCUPIED]);

        $this->service->release($this->bed->id);

        $this->assertSame(Bed::STATUS_AVAILABLE, $this->bed->refresh()->status);
    }

    public function test_setMaintenance_masuk_dan_keluar_perbaikan(): void
    {
        $this->service->setMaintenance($this->bed->id, true);
        $this->assertSame(Bed::STATUS_MAINTENANCE, $this->bed->refresh()->status);

        $this->service->setMaintenance($this->bed->id, false);
        $this->assertSame(Bed::STATUS_AVAILABLE, $this->bed->refresh()->status);
    }

    public function test_setMaintenance_bed_terisi_ditolak_422(): void
    {
        $this->bed->update(['status' => Bed::STATUS_OCCUPIED]);

        try {
            $this->service->setMaintenance($this->bed->id, true);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }
    }

    public function test_reserve_bed_kosong_mengubah_ke_reserved_dengan_ttl(): void
    {
        $this->service->reserve($this->bed->id);

        $this->bed->refresh();
        $this->assertSame(Bed::STATUS_RESERVED, $this->bed->status);
        $this->assertNotNull($this->bed->reserved_until);
        $this->assertTrue($this->bed->reserved_until->between(now()->addMinutes(59), now()->addMinutes(61)));
    }

    public function test_reserve_bed_terisi_ditolak_422(): void
    {
        $this->bed->update(['status' => Bed::STATUS_OCCUPIED]);

        $this->assertThrows(
            fn () => $this->service->reserve($this->bed->id),
            HttpException::class,
        );
    }

    public function test_reserve_bed_perbaikan_ditolak_422(): void
    {
        $this->bed->update(['status' => Bed::STATUS_MAINTENANCE]);

        $this->assertThrows(
            fn () => $this->service->reserve($this->bed->id),
            HttpException::class,
        );
    }

    public function test_occupy_dari_reserved_sukses_dan_bersihkan_reserved_until(): void
    {
        $this->service->reserve($this->bed->id);

        $this->service->occupy($this->bed->id);

        $this->bed->refresh();
        $this->assertSame(Bed::STATUS_OCCUPIED, $this->bed->status);
        $this->assertNull($this->bed->reserved_until);
    }

    public function test_releaseReservation_manual_selalu_diizinkan_walau_belum_kedaluwarsa(): void
    {
        $this->service->reserve($this->bed->id);

        $this->service->releaseReservation($this->bed->id);

        $this->bed->refresh();
        $this->assertSame(Bed::STATUS_AVAILABLE, $this->bed->status);
        $this->assertNull($this->bed->reserved_until);
    }

    public function test_releaseReservation_auto_ditolak_sebelum_kedaluwarsa(): void
    {
        $this->service->reserve($this->bed->id);

        $this->assertThrows(
            fn () => $this->service->releaseReservation($this->bed->id, auto: true),
            HttpException::class,
        );
        $this->assertSame(Bed::STATUS_RESERVED, $this->bed->refresh()->status);
    }

    public function test_releaseReservation_auto_sukses_setelah_kedaluwarsa(): void
    {
        $this->bed->update(['status' => Bed::STATUS_RESERVED, 'reserved_until' => now()->subMinute()]);

        $this->service->releaseReservation($this->bed->id, auto: true);

        $this->bed->refresh();
        $this->assertSame(Bed::STATUS_AVAILABLE, $this->bed->status);
        $this->assertNull($this->bed->reserved_until);
    }

    public function test_releaseReservation_idempoten_bila_bukan_reserved(): void
    {
        $this->service->releaseReservation($this->bed->id);

        $this->assertSame(Bed::STATUS_AVAILABLE, $this->bed->refresh()->status);
    }

    public function test_releaseExpiredReservations_hanya_melepas_yang_kedaluwarsa(): void
    {
        $expired = Bed::factory()->create(['status' => Bed::STATUS_RESERVED, 'reserved_until' => now()->subMinute()]);
        $stillValid = Bed::factory()->create(['status' => Bed::STATUS_RESERVED, 'reserved_until' => now()->addMinutes(30)]);

        $count = $this->service->releaseExpiredReservations();

        $this->assertSame(1, $count);
        $this->assertSame(Bed::STATUS_AVAILABLE, $expired->refresh()->status);
        $this->assertSame(Bed::STATUS_RESERVED, $stillValid->refresh()->status);
    }
}
