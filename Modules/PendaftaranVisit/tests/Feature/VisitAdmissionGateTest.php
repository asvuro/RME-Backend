<?php

namespace Modules\PendaftaranVisit\Tests\Feature;

use App\Events\VisitAdmitted;
use App\Modules\Contracts\HospitalConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Auth\Models\User;
use Modules\GeneralWard\Models\Ward;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Gerbang admission ala KunjunganResource::create() simgos2, kini di
 * VisitService::admit(). Catatan semantik: penolakan memakai abort_if(422)
 * sehingga respons JSON berbentuk {"message": "..."} TANPA array validation
 * errors — asersi memakai assertJsonFragment pada pesan, bukan
 * assertJsonValidationErrors.
 */
class VisitAdmissionGateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected HospitalConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');

        // Set eksplisit tiap test agar cache rs_settings tidak bocor antar-test
        // (set() otomatis flush cache rememberForever).
        $this->config = app(HospitalConfig::class);
        $this->config->set('admission.block_discharged_patient', true, 'bool');
        $this->config->set('billing.lock_on_cashier_close', true, 'bool');
        $this->config->set('admission.check_double_visit', true, 'bool');
    }

    public function test_admit_sukses_membuat_visit_dan_memicu_event(): void
    {
        Event::fake();

        $registration = Registration::factory()->create();
        $response = $this->postJson('/api/v1/visits', [
            'registration_id' => $registration->id,
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('visits', [
            'registration_id' => $registration->id,
            'received_by' => $this->user->id,
            'status' => 'active',
        ]);

        $visit = Visit::query()->where('registration_id', $registration->id)->firstOrFail();
        $this->assertNotEmpty($visit->visit_number);
        $this->assertNotNull($visit->admitted_at);

        Event::assertDispatched(VisitAdmitted::class);
    }

    public function test_admit_ditolak_bila_registrasi_sudah_pulang(): void
    {
        $registration = Registration::factory()->create();
        Visit::factory()->create([
            'registration_id' => $registration->id,
            'discharged_at' => now(),
            'final_outcome' => 'sembuh',
            'status' => 'discharged',
        ]);

        $response = $this->postJson('/api/v1/visits', [
            'registration_id' => $registration->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Pasien sudah pulang untuk registrasi ini; tidak dapat admit kunjungan baru.',
        ]);

        $this->assertSame(1, Visit::query()->where('registration_id', $registration->id)->count());
    }

    public function test_admit_ditolak_bila_tagihan_terkunci_kasir(): void
    {
        $registration = Registration::factory()->create();
        $visit = Visit::factory()->create(['registration_id' => $registration->id]);
        Invoice::factory()->locked()->create(['visit_id' => $visit->id]);

        $response = $this->postJson('/api/v1/visits', [
            'registration_id' => $registration->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Tagihan kunjungan aktif sudah dikunci oleh kasir; hubungi pembayaran.',
        ]);
    }

    public function test_admit_ditolak_karena_kunjungan_aktif_ganda(): void
    {
        $ward = Ward::factory()->create();
        $registration = Registration::factory()->create();
        Visit::factory()->create([
            'registration_id' => $registration->id,
            'ward_id' => $ward->id,
        ]);

        $response = $this->postJson('/api/v1/visits', [
            'registration_id' => $registration->id,
            'ward_id' => $ward->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Kunjungan aktif ganda terdeteksi untuk pasien/ward ini.',
        ]);
    }

    public function test_semua_gerbang_dapat_dimatikan_lewat_config(): void
    {
        $this->config->set('admission.block_discharged_patient', false, 'bool');
        $this->config->set('billing.lock_on_cashier_close', false, 'bool');
        $this->config->set('admission.check_double_visit', false, 'bool');

        $ward = Ward::factory()->create();
        $registration = Registration::factory()->create();
        $old = Visit::factory()->create([
            'registration_id' => $registration->id,
            'ward_id' => $ward->id,
            'discharged_at' => now(),
            'final_outcome' => 'sembuh',
            'status' => 'discharged',
        ]);
        Invoice::factory()->locked()->create(['visit_id' => $old->id]);

        $response = $this->postJson('/api/v1/visits', [
            'registration_id' => $registration->id,
            'ward_id' => $ward->id,
        ]);

        $response->assertCreated();
        $this->assertSame(2, Visit::query()->where('registration_id', $registration->id)->count());
    }

    public function test_visit_number_custom_diterima_sebagaimana_adanya(): void
    {
        $registration = Registration::factory()->create();
        $response = $this->postJson('/api/v1/visits', [
            'registration_id' => $registration->id,
            'visit_number' => 'RME-TEST-0001',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('visits', [
            'registration_id' => $registration->id,
            'visit_number' => 'RME-TEST-0001',
        ]);
    }
}
