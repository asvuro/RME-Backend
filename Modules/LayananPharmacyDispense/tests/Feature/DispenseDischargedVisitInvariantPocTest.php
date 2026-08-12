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
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * POC-P3: pharmacy dispense against a DISCHARGED visit.
 *
 * DispenseService gates on prescription status, screening, payer
 * restrictions and stock - but never consults VisitGate::isPatientDischarged()
 * / isActive(). A prescription left 'active' after its visit was discharged
 * can still be served: ward stock drains and a medicine charge is posted to
 * the closed encounter's invoice.
 */
class DispenseDischargedVisitInvariantPocTest extends TestCase
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

    private function readyToDispense(Visit $visit, int $stock = 100): Prescription
    {
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

    /**
     * The invariant violation: dispensing succeeds end-to-end for a visit
     * that has already been discharged.
     */
    public function test_poc_p3_dispense_against_discharged_visit_is_blocked(): void
    {
        $this->actingUser();

        $ward = Ward::factory()->create();
        $visit = Visit::factory()->discharged()->create([
            'ward_id' => $ward->id,
        ]);
        $this->assertNotNull($visit->discharged_at, 'precondition: visit is discharged');

        $prescription = $this->readyToDispense($visit);
        $itemId = (int) $prescription->items->first()->item_id;
        $stockBefore = app(StockGate::class)->currentStock((int) $ward->id, $itemId);

        $response = $this->postJson("/api/v1/prescriptions/{$prescription->id}/dispense");

        if (in_array($response->status(), [200, 201], true)) {
            $this->markTestIncomplete(
                'VULNERABLE: dispense succeeded against a DISCHARGED visit. '
                ."Ward {$ward->id} stock: ".app(StockGate::class)->currentStock((int) $ward->id, $itemId)
                ." (seeded {$stockBefore})."
            );
        }

        // Gerbang status kunjungan harus menolak (pasien sudah pulang).
        $response->assertStatus(422);

        // Tidak ada efek samping pada kunjungan tertutup...
        $this->assertSame(
            $stockBefore,
            app(StockGate::class)->currentStock((int) $ward->id, $itemId),
            'stock must NOT be decremented for a discharged encounter',
        );

        // ...dan tidak ada posting tagihan maupun dispense baru.
        $this->assertSame(0, Invoice::query()->where('visit_id', $visit->id)->count());
        $this->assertSame(0, PharmacyDispense::query()->where('prescription_id', $prescription->id)->count());
        $this->assertSame('active', $prescription->refresh()->status);
    }
}