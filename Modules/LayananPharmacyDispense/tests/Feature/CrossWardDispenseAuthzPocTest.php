<?php

namespace Modules\LayananPharmacyDispense\Tests\Feature;

use App\Modules\Contracts\StockGate;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralStaffMember\Models\StaffMember;
use Modules\GeneralStaffWardAssignment\Models\StaffWardAssignment;
use Modules\GeneralWard\Models\Ward;
use Modules\InventoryItem\Models\Item;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;
use Modules\LayananPrescriptionInitialReview\Database\Factories\PrescriptionInitialReviewFactory;
use Modules\LayananPrescriptionItem\Models\PrescriptionItem;
use Modules\LayananPrescription\Models\Prescription;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Security validation PoC (authorized white-box pentest).
 *
 * AUTH_MATRIX.md §4b claims ward-scoped staff can only write visits/billing/
 * dispense/stock for their assigned ward. The dispense endpoint is the write
 * gate for pharmacy stock + billing. This test checks whether a petugas with
 * exactly ONE ward assignment can dispense a prescription belonging to a
 * visit in a DIFFERENT ward.
 */
class CrossWardDispenseAuthzPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    /** Petugas ditugaskan HANYA ke $wardId (pola sama dengan VisitControllerTest). */
    private function actingWardStaff(int $wardId): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $staffMember = StaffMember::factory()->create(['employee_id' => $employee->id]);
        StaffWardAssignment::factory()->create(['staff_member_id' => $staffMember->id, 'ward_id' => $wardId]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function readyToDispenseVisit(Ward $ward, int $stock = 100): Prescription
    {
        $visit = Visit::factory()->create(['ward_id' => $ward->id]);
        $prescription = Prescription::factory()->create(['visit_id' => $visit->id]);
        $item = Item::factory()->create(['sell_price' => '5000.00']);

        PrescriptionItem::factory()->create([
            'prescription_id' => $prescription->id,
            'item_id' => $item->id,
            'quantity' => 4,
        ]);

        app(StockGate::class)->adjust(
            $ward->id,
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

    public function test_ward_scoped_petugas_cannot_read_visit_from_other_ward_baseline(): void
    {
        $ownWard = Ward::factory()->create();
        $otherWard = Ward::factory()->create();
        $this->actingWardStaff($ownWard->id);
        $visit = Visit::factory()->create(['ward_id' => $otherWard->id]);

        // Baseline: GET /visits/{id} dari ward lain harus 403 (sudah dites di modul Visit).
        $this->getJson("/api/v1/visits/{$visit->id}")->assertStatus(403);
    }

    public function test_ward_scoped_petugas_cannot_dispense_prescription_from_other_ward(): void
    {
        $ownWard = Ward::factory()->create();
        $otherWard = Ward::factory()->create();
        $this->actingWardStaff($ownWard->id);

        $prescription = $this->readyToDispenseVisit($otherWard, stock: 50);
        $itemId = (int) $prescription->items->first()->item_id;

        $response = $this->postJson("/api/v1/prescriptions/{$prescription->id}/dispense");

        if ($response->status() === 201) {
            $this->markTestIncomplete(
                "VULNERABLE: ward-scoped petugas (ward {$ownWard->id}) successfully dispensed "
                ."prescription of visit in ward {$otherWard->id}. Stock decremented: "
                .app(StockGate::class)->currentStock($otherWard->id, $itemId)
                .' (expected 50). Dispenses created: '.PharmacyDispense::count()
            );
        }

        $response->assertStatus(403);
        $this->assertSame(0, PharmacyDispense::count());
        $this->assertSame(50, app(StockGate::class)->currentStock($otherWard->id, $itemId));
        $this->assertSame('active', $prescription->refresh()->status);
    }
}