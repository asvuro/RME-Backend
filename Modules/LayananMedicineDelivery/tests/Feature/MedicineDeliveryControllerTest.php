<?php

namespace Modules\LayananMedicineDelivery\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananMedicineDelivery\Models\MedicineDelivery;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;
use Tests\TestCase;

class MedicineDeliveryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    /** Dispense farmasi yang benar-benar sudah menyerahkan obat ke pasien/kurir. */
    private function dispensedDispense(): PharmacyDispense
    {
        return PharmacyDispense::factory()->create(['status' => 'dispensed']);
    }

    public function test_it_lists_medicine_deliveries(): void
    {
        $this->actingUser();

        MedicineDelivery::factory()->count(3)->create();

        $this->getJson('/api/v1/medicine-deliveries')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_it_filters_deliveries_by_status(): void
    {
        $this->actingUser();

        MedicineDelivery::factory()->create(['status' => MedicineDelivery::STATUS_PENDING]);
        MedicineDelivery::factory()->create([
            'status' => MedicineDelivery::STATUS_DIKIRIM,
            'courier_employee_id' => Employee::factory(),
        ]);

        $this->getJson('/api/v1/medicine-deliveries?status=dikirim')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_petugas_can_schedule_delivery_for_dispensed_medicine(): void
    {
        $this->actingUser();

        $dispense = $this->dispensedDispense();

        $this->postJson('/api/v1/medicine-deliveries', [
            'pharmacy_dispense_id' => $dispense->id,
            'patient_address' => 'Jl. Melati No. 10, Bandung',
        ])->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.patient_address', 'Jl. Melati No. 10, Bandung');

        $delivery = MedicineDelivery::query()->where('pharmacy_dispense_id', $dispense->id)->firstOrFail();

        // Status awal selalu 'pending' (tidak dari input), requested_at
        // terisi otomatis, belum ada kurir maupun waktu serah terima.
        $this->assertSame(MedicineDelivery::STATUS_PENDING, $delivery->status);
        $this->assertNotNull($delivery->requested_at);
        $this->assertNull($delivery->courier_employee_id);
        $this->assertNull($delivery->delivered_at);
    }

    public function test_store_rejects_medicine_not_yet_dispensed(): void
    {
        $this->actingUser();

        // Farmasi masih memproses resep (pending): tidak ada barang fisik
        // untuk dibawa kurir - gerbang service harus menolak.
        $dispense = PharmacyDispense::factory()->create(['status' => 'pending']);

        $this->postJson('/api/v1/medicine-deliveries', [
            'pharmacy_dispense_id' => $dispense->id,
            'patient_address' => 'Jl. Melati No. 10, Bandung',
        ])->assertStatus(422);

        $this->assertSame(0, MedicineDelivery::count());
    }

    public function test_store_rejects_second_delivery_for_same_dispense(): void
    {
        $this->actingUser();

        $dispense = $this->dispensedDispense();

        $payload = [
            'pharmacy_dispense_id' => $dispense->id,
            'patient_address' => 'Jl. Melati No. 10, Bandung',
        ];

        $this->postJson('/api/v1/medicine-deliveries', $payload)->assertCreated();
        $this->postJson('/api/v1/medicine-deliveries', $payload)->assertStatus(422);

        $this->assertSame(1, MedicineDelivery::count());
    }

    public function test_assign_courier_moves_pending_to_dikirim(): void
    {
        $this->actingUser();

        $delivery = MedicineDelivery::factory()->create();
        $courier = Employee::factory()->create(['is_active' => true]);

        $this->postJson("/api/v1/medicine-deliveries/{$delivery->id}/assign-courier", [
            'courier_employee_id' => $courier->id,
        ])->assertOk()
            ->assertJsonPath('data.status', MedicineDelivery::STATUS_DIKIRIM)
            ->assertJsonPath('data.courier_employee_id', $courier->id);

        $this->assertSame($courier->id, $delivery->refresh()->courier_employee_id);
    }

    public function test_mark_delivered_requires_courier_and_shipped_status_then_records_time(): void
    {
        $this->actingUser();

        $dispense = $this->dispensedDispense();
        $this->postJson('/api/v1/medicine-deliveries', [
            'pharmacy_dispense_id' => $dispense->id,
            'patient_address' => 'Jl. Melati No. 10, Bandung',
        ])->assertCreated();

        $deliveryId = MedicineDelivery::query()->where('pharmacy_dispense_id', $dispense->id)->firstOrFail()->id;

        // Gerbang 1: belum ada kurir - serah terima tidak bisa dicatat.
        $this->postJson("/api/v1/medicine-deliveries/{$deliveryId}/mark-delivered")
            ->assertStatus(422);
        $this->assertSame(MedicineDelivery::STATUS_PENDING, MedicineDelivery::find($deliveryId)->status);

        // Kurir ditugaskan: paket berangkat (pending -> dikirim).
        $courier = Employee::factory()->create(['is_active' => true]);
        $this->postJson("/api/v1/medicine-deliveries/{$deliveryId}/assign-courier", [
            'courier_employee_id' => $courier->id,
        ])->assertOk();

        // Happy path: diterima + delivered_at tercatat sekarang.
        $this->postJson("/api/v1/medicine-deliveries/{$deliveryId}/mark-delivered")
            ->assertOk()
            ->assertJsonPath('data.status', MedicineDelivery::STATUS_DITERIMA);

        $delivery = MedicineDelivery::find($deliveryId);
        $this->assertSame(MedicineDelivery::STATUS_DITERIMA, $delivery->status);
        $this->assertNotNull($delivery->delivered_at);

        // Idempoten: menandai diterima dua kali ditolak.
        $this->postJson("/api/v1/medicine-deliveries/{$deliveryId}/mark-delivered")
            ->assertStatus(422);
    }

    public function test_mark_delivered_rejects_pending_jump_even_with_courier_recorded(): void
    {
        // Lompatan status dilarang: walaupun kolom kurir sudah terisi,
        // pengantaran yang tidak pernah "berangkat" lewat assign-courier
        // tidak boleh langsung dilompatkan ke diterima.
        $courier = Employee::factory()->create(['is_active' => true]);
        $delivery = MedicineDelivery::factory()->create([
            'status' => MedicineDelivery::STATUS_PENDING,
            'courier_employee_id' => $courier->id,
        ]);

        $this->actingUser();

        $this->postJson("/api/v1/medicine-deliveries/{$delivery->id}/mark-delivered")
            ->assertStatus(422);

        $this->assertSame(MedicineDelivery::STATUS_PENDING, $delivery->refresh()->status);
        $this->assertNull($delivery->refresh()->delivered_at);
    }

    public function test_only_the_linked_courier_account_can_mark_delivered(): void
    {
        // Kurir terhubung akun user: petugas lain tidak boleh melapor
        // "diterima" atas nama kurir tersebut (gerbang 403 di service).
        $kurirAccount = User::factory()->create();
        $courier = Employee::factory()->create(['is_active' => true, 'user_id' => $kurirAccount->id]);

        $delivery = MedicineDelivery::factory()->create([
            'status' => MedicineDelivery::STATUS_DIKIRIM,
            'courier_employee_id' => $courier->id,
        ]);

        $this->actingUser(); // petugas lain, bukan akun kurir.

        $this->postJson("/api/v1/medicine-deliveries/{$delivery->id}/mark-delivered")
            ->assertStatus(403);

        $this->assertSame(MedicineDelivery::STATUS_DIKIRIM, $delivery->refresh()->status);
    }

    public function test_address_is_locked_after_delivery_is_finished(): void
    {
        $this->actingUser();

        $delivery = MedicineDelivery::factory()->create(['status' => MedicineDelivery::STATUS_DITERIMA]);

        $this->putJson("/api/v1/medicine-deliveries/{$delivery->id}", [
            'patient_address' => 'Alamat baru',
        ])->assertStatus(422);

        $this->assertNotSame('Alamat baru', $delivery->refresh()->patient_address);
    }
}
