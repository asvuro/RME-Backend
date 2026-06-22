<?php

namespace Modules\PendaftaranVisit\Tests\Feature;

use App\Events\VisitDischarged;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralRoom\Models\Room;
use Modules\GeneralWard\Models\Ward;
use Modules\GeneralWardTariff\Models\WardTariff;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Gerbang pulang — port STATUS kunjungan → 2 pada trigger
 * onAfterUpdateKunjungan: bed dibebaskan, rekam pulang tercipta,
 * akomodasi rawat inap diposting ala pembayaran.storeAkomodasi.
 */
class VisitDischargeApiTest extends TestCase
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

    protected function admittedInBed(): Visit
    {
        $bed = Bed::factory()->create([
            'room_id' => Room::factory()->create(['ward_id' => $this->ward])->id,
        ]);

        $visit = Visit::factory()->create([
            'registration_id' => Registration::factory(),
            'ward_id' => $this->ward->id,
            'bed_id' => $bed->id,
            // 50 jam jauh dari batas pembulatan hari agar lama dirawat stabil = 3.
            'admitted_at' => now()->subHours(50),
        ]);
        $bed->update(['status' => Bed::STATUS_OCCUPIED]);

        return $visit;
    }

    public function test_tamu_ditolak_401(): void
    {
        $this->app['auth']->guard('sanctum')->forgetUser();
        $visit = Visit::factory()->create();

        $this->postJson("/api/v1/visits/{$visit->id}/discharge", ['final_outcome' => 'sembuh'])
            ->assertUnauthorized();
    }

    public function test_pulang_mengosongkan_bed_dan_merekam_discharge(): void
    {
        Event::fake([VisitDischarged::class]);
        $visit = $this->admittedInBed();

        $response = $this->postJson("/api/v1/visits/{$visit->id}/discharge", [
            'final_outcome' => 'sembuh',
            'discharge_method' => 'pulang atas izin dokter',
            'follow_up_notes' => 'Kontrol poliklinik seminggu',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'discharged')
            ->assertJsonPath('data.final_outcome', 'sembuh');

        // Bed bebas + kunjungan tertutup oleh aktor yang memulangkan.
        $this->assertSame(Bed::STATUS_AVAILABLE, $visit->bed->refresh()->status);
        $this->assertNotNull($visit->refresh()->discharged_at);
        $this->assertSame($this->user->id, (int) $visit->final_outcome_by);

        $this->assertDatabaseHas('patient_discharge_records', [
            'visit_id' => $visit->id,
            'discharge_method' => 'pulang atas izin dokter',
        ]);

        Event::assertDispatched(VisitDischarged::class);
    }

    public function test_pulang_memposting_akomodasi_bila_tarif_terpasang(): void
    {
        WardTariff::factory()->create([
            'ward_id' => $this->ward->id,
            'room_class_id' => null,
            'price' => '350000.00',
        ]);
        $visit = $this->admittedInBed();

        $this->postJson("/api/v1/visits/{$visit->id}/discharge", [
            'final_outcome' => 'sembuh',
            'discharge_method' => 'pulang atas izin dokter',
        ])
            ->assertOk();

        $invoice = Invoice::query()->where('visit_id', $visit->id)->firstOrFail();
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'category' => 'accommodation',
            'quantity' => 3,
            'unit_price' => '350000.00',
        ]);
    }

    public function test_pulang_tanpa_tarif_tidak_memposting_akomodasi(): void
    {
        $visit = $this->admittedInBed();

        $this->postJson("/api/v1/visits/{$visit->id}/discharge", [
            'final_outcome' => 'sembuh',
            'discharge_method' => 'rujuk',
        ])
            ->assertOk();

        $this->assertNull(Invoice::query()->where('visit_id', $visit->id)->first());
    }

    public function test_pulang_ulang_ditolak_422(): void
    {
        $visit = $this->admittedInBed();

        $this->postJson("/api/v1/visits/{$visit->id}/discharge", [
            'final_outcome' => 'sembuh',
            'discharge_method' => 'pulang atas izin dokter',
        ])->assertOk();

        $this->postJson("/api/v1/visits/{$visit->id}/discharge", [
            'final_outcome' => 'meninggal',
            'discharge_method' => 'meninggal',
        ])
            ->assertStatus(422);
    }

    public function test_final_outcome_wajib_diisi(): void
    {
        $visit = Visit::factory()->create();

        $this->postJson("/api/v1/visits/{$visit->id}/discharge", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['final_outcome']);
    }
}
