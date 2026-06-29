<?php

namespace Modules\LayananPharmacyDispense\Tests\Feature;

use App\Modules\Contracts\StockGate;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralWard\Models\Ward;
use Modules\InventoryItem\Models\Item;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;
use Modules\LayananPrescriptionInitialReview\Database\Factories\PrescriptionInitialReviewFactory;
use Modules\LayananPrescriptionItem\Models\PrescriptionItem;
use Modules\LayananPrescription\Models\Prescription;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class PharmacyDispenseControllerTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }
    private function actingUser(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_it_lists_dispenses(): void
    {
        $this->actingUser();
        PharmacyDispense::factory()->count(3)->create();

        $this->getJson('/api/v1/pharmacy-dispenses')->assertOk()->assertJsonCount(3, 'data');
    }

    /**
     * store() sekarang selalu lewat DispenseService::dispense() (anti-bypass):
     * resep harus benar-benar siap dilayani (telaah lulus, stok cukup, dsb.),
     * quantity/status di body TIDAK dipakai - hasil murni dari gerbang service.
     */
    private function readyToDispense(int $stock = 100): Prescription
    {
        $visit = Visit::factory()->create(['ward_id' => Ward::factory()]);
        $prescription = Prescription::factory()->create(['visit_id' => $visit->id]);
        $item = Item::factory()->create(['sell_price' => '5000.00']);

        PrescriptionItem::factory()->create([
            'prescription_id' => $prescription->id,
            'item_id' => $item->id,
            'quantity' => 4,
        ]);

        app(StockGate::class)->adjust(
            (int) $visit->ward_id,
            $item->id,
            'in',
            $stock,
            User::factory()->create(),
        );

        PrescriptionInitialReviewFactory::new()->create([
            'prescription_id' => $prescription->id,
            'is_appropriate' => true,
            'issues_found' => null,
        ]);

        return $prescription;
    }

    public function test_it_creates_dispense(): void
    {
        $this->actingUser();
        $prescription = $this->readyToDispense();

        $this->postJson('/api/v1/pharmacy-dispenses', [
            'prescription_id' => $prescription->id,
            'quantity' => 5,
            'status' => 'pending',
        ])->assertCreated();

        $this->assertDatabaseCount('pharmacy_dispenses', 1);
        // Hasil datang dari gerbang service, bukan dari input mentah.
        $this->assertSame('dispensed', PharmacyDispense::first()->status);
        $this->assertSame(4, PharmacyDispense::first()->quantity);
        $this->assertSame('dispensed', $prescription->refresh()->status);
    }

    public function test_it_rejects_create_when_prescription_not_ready(): void
    {
        // Resep belum ditelaah apoteker - gerbang service harus menolak,
        // membuktikan store() tidak lagi membuat record secara mentah.
        $this->actingUser();
        $prescription = Prescription::factory()->create();

        $this->postJson('/api/v1/pharmacy-dispenses', [
            'prescription_id' => $prescription->id,
            'quantity' => 5,
            'status' => 'pending',
        ])->assertStatus(422);

        $this->assertDatabaseCount('pharmacy_dispenses', 0);
    }

    public function test_it_shows_dispense(): void
    {
        $this->actingUser();
        $dispense = PharmacyDispense::factory()->create();

        $this->getJson("/api/v1/pharmacy-dispenses/{$dispense->id}")->assertOk()->assertJsonPath('data.id', $dispense->id);
    }

    public function test_it_cancels_dispense_and_restores_stock_and_prescription(): void
    {
        $this->actingUser();
        $prescription = $this->readyToDispense(stock: 50);
        $wardId = (int) $prescription->visit->ward_id;
        $itemId = (int) $prescription->items->first()->item_id;

        $this->postJson('/api/v1/pharmacy-dispenses', [
            'prescription_id' => $prescription->id,
            'quantity' => 4,
            'status' => 'pending',
        ])->assertCreated();

        $dispense = PharmacyDispense::query()->where('prescription_id', $prescription->id)->firstOrFail();
        $this->assertSame(46, app(StockGate::class)->currentStock($wardId, $itemId));

        $this->putJson("/api/v1/pharmacy-dispenses/{$dispense->id}", [
            'status' => 'cancelled',
        ])->assertOk()->assertJsonPath('data.status', 'cancelled');

        $this->assertSame('cancelled', $dispense->refresh()->status);
        $this->assertSame('active', $prescription->refresh()->status);
        // Stok dikembalikan penuh.
        $this->assertSame(50, app(StockGate::class)->currentStock($wardId, $itemId));
    }

    public function test_it_rejects_update_that_changes_more_than_status_cancelled(): void
    {
        // Bypass lama: bebas mengubah quantity/status apa pun - sekarang ditolak.
        $this->actingUser();
        $dispense = PharmacyDispense::factory()->create(['status' => 'pending']);

        $this->putJson("/api/v1/pharmacy-dispenses/{$dispense->id}", [
            'quantity' => 999,
        ])->assertStatus(422);

        $this->assertNotSame(999, $dispense->refresh()->quantity);
    }
}
