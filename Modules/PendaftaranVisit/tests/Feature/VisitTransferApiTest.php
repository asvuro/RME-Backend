<?php

namespace Modules\PendaftaranVisit\Tests\Feature;

use App\Events\VisitTransferred;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralRoom\Models\Room;
use Modules\GeneralWard\Models\Ward;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Gerbang mutasi antar bed — port pendaftaran.mutasi simgos2:
 * kunjungan pindah, riwayat tercipta, okupansi kedua bed bertukar.
 */
class VisitTransferApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Ward $ward;

    protected function setUp(): void
    {
        parent::setUp();


        $this->seed(RoleAndPermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('petugas');
        $this->actingAs($this->user, 'sanctum');
        $this->ward = Ward::factory()->create();
    }

    protected function makeBed(): Bed
    {
        return Bed::factory()->create([
            'room_id' => Room::factory()->create(['ward_id' => $this->ward])->id,
        ]);
    }

    public function test_tamu_ditolak_401(): void
    {
        $this->app['auth']->guard('sanctum')->forgetUser();
        $visit = Visit::factory()->create();
        $bed = $this->makeBed();

        $this->postJson("/api/v1/visits/{$visit->id}/transfer", ['target_bed_id' => $bed->id])
            ->assertUnauthorized();
    }

    public function test_transfer_memindahkan_okupansi_dan_mencatat_riwayat(): void
    {
        Event::fake([VisitTransferred::class]);

        $bedLama = $this->makeBed();
        $bedBaru = $this->makeBed();

        $visit = Visit::factory()->create([
            'ward_id' => $this->ward->id,
            'bed_id' => $bedLama->id,
        ]);
        $bedLama->update(['status' => Bed::STATUS_OCCUPIED]);

        $response = $this->postJson("/api/v1/visits/{$visit->id}/transfer", [
            'target_bed_id' => $bedBaru->id,
            'notes' => 'Pasien dimutasi ke bed jendela',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.bed_to_id', $bedBaru->id)
            ->assertJsonPath('data.bed_from_id', $bedLama->id);

        // Okupansi bertukar: tujuan terisi, lama bebas.
        $this->assertSame(Bed::STATUS_OCCUPIED, $bedBaru->refresh()->status);
        $this->assertSame(Bed::STATUS_AVAILABLE, $bedLama->refresh()->status);

        // Kunjungan menunjuk bed baru + riwayat mutasi tercipta.
        $this->assertDatabaseHas('visits', ['id' => $visit->id, 'bed_id' => $bedBaru->id]);
        $this->assertDatabaseHas('visit_transfers', [
            'visit_id' => $visit->id,
            'bed_from_id' => $bedLama->id,
            'bed_to_id' => $bedBaru->id,
            'transferred_by' => $this->user->id,
        ]);

        Event::assertDispatched(VisitTransferred::class);
    }

    public function test_transfer_ke_bed_terisi_pasien_lain_ditolak_422(): void
    {
        $visit = Visit::factory()->create(['ward_id' => $this->ward->id]);

        $bedTerisi = $this->makeBed();
        $bedTerisi->update(['status' => Bed::STATUS_OCCUPIED]);

        $this->postJson("/api/v1/visits/{$visit->id}/transfer", ['target_bed_id' => $bedTerisi->id])
            ->assertStatus(422);

        // Kunjungan tidak berpindah dan bed tetap terisi pemiliknya.
        $this->assertNull($visit->refresh()->bed_id);
        $this->assertSame(Bed::STATUS_OCCUPIED, $bedTerisi->refresh()->status);
        $this->assertDatabaseMissing('visit_transfers', ['visit_id' => $visit->id]);
    }

    public function test_transfer_untuk_kunjungan_sudah_pulang_ditolak_422(): void
    {
        $visit = Visit::factory()->discharged()->create();
        $bed = $this->makeBed();

        $this->postJson("/api/v1/visits/{$visit->id}/transfer", ['target_bed_id' => $bed->id])
            ->assertStatus(422);

        $this->assertSame(Bed::STATUS_AVAILABLE, $bed->refresh()->status);
    }

    public function test_transfer_ke_bed_yang_sama_ditolak_422(): void
    {
        $bed = $this->makeBed();
        $visit = Visit::factory()->create(['ward_id' => $this->ward->id, 'bed_id' => $bed->id]);
        $bed->update(['status' => Bed::STATUS_OCCUPIED]);

        $this->postJson("/api/v1/visits/{$visit->id}/transfer", ['target_bed_id' => $bed->id])
            ->assertStatus(422);
    }

    public function test_target_bed_wajib_diisi(): void
    {
        $visit = Visit::factory()->create();

        $this->postJson("/api/v1/visits/{$visit->id}/transfer", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['target_bed_id']);
    }
}
