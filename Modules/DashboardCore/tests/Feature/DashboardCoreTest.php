<?php

namespace Modules\DashboardCore\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralRoom\Models\Room;
use Modules\GeneralWard\Models\Ward;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;
use Modules\LayananPrescription\Models\Prescription;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranPayment\Models\Payment;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Satu endpoint KPI inti: okupansi live, arus kunjungan, tagihan, farmasi,
 * dan tren tujuh hari.
 */
class DashboardCoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_kpi_okupansi_dan_arus_kunjungan(): void
    {
        $ward = Ward::factory()->create();
        $room = Room::factory()->create(['ward_id' => $ward]);
        [$bedA] = Bed::factory()->count(2)->create(['room_id' => $room->id]);

        // Pasien menginap sejak kemarin di bedA.
        $registration = Registration::factory()->create();
        Visit::factory()->create([
            'registration_id' => $registration->id,
            'ward_id' => $ward->id,
            'bed_id' => $bedA->id,
            'admitted_at' => now()->subDay(),
        ]);
        $bedA->update(['status' => Bed::STATUS_OCCUPIED]);

        // Admit hari ini tanpa bed.
        Visit::factory()->create([
            'registration_id' => Registration::factory(),
            'ward_id' => $ward->id,
            'admitted_at' => now(),
        ]);

        $data = $this->getJson('/api/v1/dashboard/core')->assertOk()->json('data');

        $this->assertSame(2, $data['occupancy']['total_beds']);
        $this->assertSame(1, $data['occupancy']['occupied']);
        // JSON tanpa desimal ter-decode sebagai int — bandingkan longgar.
        $this->assertEqualsWithDelta(50.0, $data['occupancy']['occupancy_rate'], 0.01);
        $this->assertSame(2, $data['inpatients_active']);
        $this->assertSame(1, $data['admissions_today']);
    }

    public function test_pulang_hari_ini_terhitung_dan_bisa_dilihat_per_tanggal(): void
    {
        $ward = Ward::factory()->create();
        $kemarin = now()->subDay();

        Visit::factory()->create([
            'registration_id' => Registration::factory(),
            'ward_id' => $ward->id,
            'admitted_at' => $kemarin->copy()->subDay(),
            'discharged_at' => $kemarin,
            'status' => 'discharged',
        ]);

        $data = $this->getJson('/api/v1/dashboard/core?date='.$kemarin->toDateString())
            ->assertOk()
            ->json('data');

        $this->assertSame($kemarin->toDateString(), $data['date']);
        $this->assertSame(1, $data['discharges_today']);
        $this->assertSame(0, $data['admissions_today']);
    }

    public function test_tagihan_dan_pembayaran_hari_ini(): void
    {
        $invoice = Invoice::factory()->create(['invoice_date' => now(), 'total_amount' => '150000']);
        Invoice::factory()->create(['invoice_date' => now()->subDay(), 'total_amount' => '999999']);

        // Ikat ke invoice eksplisit — default factory Payment akan membuat invoice baru.
        Payment::factory()->create(['invoice_id' => $invoice->id, 'paid_at' => now(), 'amount' => '50000']);

        $data = $this->getJson('/api/v1/dashboard/core')->assertOk()->json('data');

        $this->assertSame(1, $data['invoices_today']['count']);
        $this->assertEqualsWithDelta(150000.0, $data['invoices_today']['total_amount'], 0.01);
        $this->assertSame(1, $data['payments_today']['count']);
        $this->assertEqualsWithDelta(50000.0, $data['payments_today']['total_amount'], 0.01);
    }

    public function test_resep_dibuat_dan_dilayani_hari_ini(): void
    {
        Prescription::factory()->create(['prescribed_at' => now()]);
        Prescription::factory()->create(['prescribed_at' => now()]);
        $layani = Prescription::factory()->create(['prescribed_at' => now()]);

        PharmacyDispense::factory()->create([
            'prescription_id' => $layani->id,
            'dispensed_at' => now(),
        ]);

        $data = $this->getJson('/api/v1/dashboard/core')->assertOk()->json('data');

        $this->assertSame(3, $data['prescriptions_today']['created']);
        $this->assertSame(1, $data['prescriptions_today']['dispensed']);
    }

    public function test_tren_menghasilkan_tujuh_hari_urut(): void
    {
        $data = $this->getJson('/api/v1/dashboard/core')->assertOk()->json('data.trend');

        $this->assertCount(7, $data);
        $this->assertSame(now()->subDays(6)->toDateString(), $data[0]['date']);
        $this->assertSame(now()->toDateString(), $data[6]['date']);
    }

    public function test_endpoint_tertutup_untuk_tamu(): void
    {
        $this->app['auth']->guard('sanctum')->forgetUser();

        $this->getJson('/api/v1/dashboard/core')->assertUnauthorized();
    }
}
