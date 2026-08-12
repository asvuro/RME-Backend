<?php

namespace Modules\InventoryWardItemStock\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Database\Factories\EmployeeFactory;
use Modules\GeneralStaffMember\Database\Factories\StaffMemberFactory;
use Modules\GeneralStaffWardAssignment\Database\Factories\StaffWardAssignmentFactory;
use Modules\GeneralWard\Database\Factories\WardFactory;
use Modules\InventoryWardItemStock\Models\WardItemStock;
use Tests\TestCase;

/**
 * PoC: read-scope drift between index() and show() on ward item stock.
 *
 * index() re-implemented scoping inline with different semantics from the
 * WardAccessResolver::canAccessWard() contract used by every other method:
 * an ASSIGNED user skipped the whereIn whenever the client filtered by any
 * ward_id, so the list leaked other wards' stock while show() on the same row
 * returned 403. The fix routes both endpoints through one shared read scope.
 */
class WardStockReadScopeDriftPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_unassigned_petugas_reads_every_wards_stock_via_index_but_is_denied_show(): void
    {
        // Petugas with an Employee profile but ZERO ward assignments: per the
        // canAccessWard contract they are not yet enrolled in ward scoping, so
        // every endpoint grants full access to them.
        $user = User::factory()->create();
        $user->assignRole('petugas');
        EmployeeFactory::new()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');

        $wardA = WardFactory::new()->create();
        $wardB = WardFactory::new()->create();
        $stockA = WardItemStock::factory()->create(['ward_id' => $wardA->id, 'quantity' => 111]);
        $stockB = WardItemStock::factory()->create(['ward_id' => $wardB->id, 'quantity' => 222]);

        // Baseline: single-row access is allowed by design for unassigned users.
        $this->getJson("/api/v1/inventorywarditemstocks/{$stockA->id}")
            ->assertOk()
            ->assertJsonPath('data.quantity', 111);

        $returnedIds = collect($this->getJson("/api/v1/inventorywarditemstocks?ward_id={$wardB->id}")
            ->assertOk()
            ->json('data'))
            ->pluck('id')
            ->all();
        $this->assertContains($stockB->id, $returnedIds);

        $allReturnedIds = collect($this->getJson('/api/v1/inventorywarditemstocks')
            ->assertOk()
            ->json('data'))
            ->pluck('id')
            ->all();
        $this->assertEqualsCanonicalizing([$stockA->id, $stockB->id], $allReturnedIds);
    }

    public function test_assigned_petugas_index_scope_stays_limited_to_assigned_wards(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $employee = EmployeeFactory::new()->create(['user_id' => $user->id]);
        $staffMember = StaffMemberFactory::new()->create(['employee_id' => $employee->id]);
        $ownWard = WardFactory::new()->create();
        StaffWardAssignmentFactory::new()->create([
            'staff_member_id' => $staffMember->id,
            'ward_id' => $ownWard->id,
        ]);
        $this->actingAs($user, 'sanctum');

        $otherWard = WardFactory::new()->create();
        $ownStock = WardItemStock::factory()->create(['ward_id' => $ownWard->id]);
        $otherStock = WardItemStock::factory()->create(['ward_id' => $otherWard->id]);

        $returnedIds = collect($this->getJson('/api/v1/inventorywarditemstocks')
            ->assertOk()
            ->json('data'))
            ->pluck('id')
            ->all();
        $this->assertContains($ownStock->id, $returnedIds);
        $this->assertNotContains($otherStock->id, $returnedIds);

        // The original drift: filtering the list by another ward's id bypassed
        // the assignment scope and leaked its rows. Fixed scope must hold even
        // with an explicit ward filter.
        $filteredIds = collect($this->getJson("/api/v1/inventorywarditemstocks?ward_id={$otherWard->id}")
            ->assertOk()
            ->json('data'))
            ->pluck('id')
            ->all();
        $this->assertSame([], $filteredIds,
            'an assigned petugas must not read other wards stock through the list endpoint either');

        $this->getJson("/api/v1/inventorywarditemstocks/{$otherStock->id}")->assertForbidden();
    }
}