<?php

namespace Modules\PendaftaranVisit\Tests\Unit;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralRoom\Models\Room;
use Modules\GeneralWard\Models\Ward;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;
use Modules\PendaftaranVisit\Services\VisitService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Menutup celah yang disengaja di #7: konflik bed lintas-pasien kini
 * dicek atomik lewat BedGate saat admit (port trigger onAfterInsertKunjungan).
 */
class VisitAdmissionBedTest extends TestCase
{
    use RefreshDatabase;

    protected VisitService $service;

    protected User $user;

    protected Ward $ward;

    protected Bed $bed;

    protected function setUp(): void
    {
        parent::setUp();


        $this->seed(RoleAndPermissionSeeder::class);
        $this->service = app(VisitService::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('petugas');
        $this->ward = Ward::factory()->create();
        $this->bed = Bed::factory()->create(['room_id' => Room::factory()->create(['ward_id' => $this->ward])->id]);
    }

    public function test_admit_dengan_bed_menandai_bed_terisi(): void
    {
        $registration = Registration::factory()->create();

        $visit = $this->service->admit([
            'registration_id' => $registration->id,
            'ward_id' => $this->ward->id,
            'bed_id' => $this->bed->id,
        ], $this->user);

        $this->assertSame($this->bed->id, $visit->bed_id);
        $this->assertSame(Bed::STATUS_OCCUPIED, $this->bed->refresh()->status);
    }

    public function test_admit_ke_bed_pasien_lain_ditolak_422(): void
    {
        $pertama = Registration::factory()->create();
        $kedua = Registration::factory()->create();

        $this->service->admit([
            'registration_id' => $pertama->id,
            'ward_id' => $this->ward->id,
            'bed_id' => $this->bed->id,
        ], $this->user);

        try {
            $this->service->admit([
                'registration_id' => $kedua->id,
                'ward_id' => $this->ward->id,
                'bed_id' => $this->bed->id,
            ], $this->user);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }

        // Tidak ada kunjungan kedua yang tercipta.
        $this->assertSame(1, Visit::query()
            ->where('bed_id', $this->bed->id)
            ->count());
    }

    public function test_admit_bed_beda_ward_ditolak_422(): void
    {
        $wardLain = Ward::factory()->create();
        $registration = Registration::factory()->create();

        try {
            $this->service->admit([
                'registration_id' => $registration->id,
                'ward_id' => $wardLain->id,
                'bed_id' => $this->bed->id,
            ], $this->user);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }

        $this->assertSame(Bed::STATUS_AVAILABLE, $this->bed->refresh()->status);
    }

    public function test_admit_tanpa_bed_tetap_berjalan_normal(): void
    {
        $registration = Registration::factory()->create();

        $visit = $this->service->admit(['registration_id' => $registration->id], $this->user);

        $this->assertNull($visit->bed_id);
        $this->assertNotNull($visit->visit_number);
        $this->assertSame(Bed::STATUS_AVAILABLE, $this->bed->refresh()->status);
    }

    public function test_admit_bed_perbaikan_ditolak_422(): void
    {
        $this->bed->update(['status' => Bed::STATUS_MAINTENANCE]);
        $registration = Registration::factory()->create();

        $this->assertThrows(
            fn () => $this->service->admit([
                'registration_id' => $registration->id,
                'ward_id' => $this->ward->id,
                'bed_id' => $this->bed->id,
            ], $this->user),
            HttpException::class,
        );
    }
}
