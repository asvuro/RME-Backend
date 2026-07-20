<?php

namespace Modules\InventoryWardStockTransaction\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralStaffMember\Models\StaffMember;
use Modules\GeneralStaffWardAssignment\Models\StaffWardAssignment;
use Modules\GeneralWard\Models\Ward;
use Modules\InventoryItem\Models\Item;
use Modules\InventoryWardItemStock\Models\WardItemStock;
use Tests\TestCase;

class WardStockTransactionControllerTest extends TestCase
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

    /** Petugas yang ditugaskan HANYA ke $wardId (least-privilege #3). */
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

    public function test_ward_staff_cannot_record_transaction_for_another_ward(): void
    {
        $ownWard = Ward::factory()->create();
        $otherWard = Ward::factory()->create();
        $this->actingWardStaff($ownWard->id);
        $item = Item::factory()->create();

        $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => $otherWard->id,
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => 5,
        ])->assertStatus(403);
    }

    public function test_ward_staff_cannot_read_transaction_from_another_ward(): void
    {
        $ownWard = Ward::factory()->create();
        $otherWard = Ward::factory()->create();
        $item = Item::factory()->create();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');
        $rOther = $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => $otherWard->id, 'item_id' => $item->id, 'type' => 'in', 'quantity' => 5,
        ]);
        $otherTransactionId = $rOther->json('data.id');

        $this->actingWardStaff($ownWard->id);

        $this->getJson("/api/v1/ward-stock-transactions/{$otherTransactionId}")->assertStatus(403);
    }

    public function test_ward_staff_list_excludes_transactions_from_other_wards(): void
    {
        $ownWard = Ward::factory()->create();
        $otherWard = Ward::factory()->create();
        $item = Item::factory()->create();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');
        $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => $otherWard->id, 'item_id' => $item->id, 'type' => 'in', 'quantity' => 5,
        ]);

        $this->actingWardStaff($ownWard->id);
        $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => $ownWard->id, 'item_id' => $item->id, 'type' => 'in', 'quantity' => 3,
        ]);

        $response = $this->getJson('/api/v1/ward-stock-transactions');

        $response->assertOk();
        foreach ($response->json('data') as $row) {
            $this->assertSame($ownWard->id, $row['ward_id']);
        }
    }

    public function test_ward_staff_can_record_transaction_for_own_ward(): void
    {
        $ownWard = Ward::factory()->create();
        $this->actingWardStaff($ownWard->id);
        $item = Item::factory()->create();

        $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => $ownWard->id,
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => 5,
        ])->assertCreated();
    }

    public function test_it_records_an_in_transaction_and_increments_ward_stock(): void
    {
        $user = $this->actingUser();
        $ward = Ward::factory()->create();
        $item = Item::factory()->create();

        $response = $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => $ward->id,
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => 20,
        ]);

        $response->assertCreated()->assertJsonPath('data.type', 'in');
        $this->assertDatabaseHas('ward_stock_transactions', ['ward_id' => $ward->id, 'performed_by' => $user->id]);
        $this->assertEquals(20, WardItemStock::where('ward_id', $ward->id)->where('item_id', $item->id)->value('quantity'));
    }

    public function test_it_records_an_out_transaction_and_decrements_ward_stock(): void
    {
        $this->actingUser();
        $ward = Ward::factory()->create();
        $item = Item::factory()->create();
        WardItemStock::create(['ward_id' => $ward->id, 'item_id' => $item->id, 'quantity' => 30]);

        $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => $ward->id,
            'item_id' => $item->id,
            'type' => 'out',
            'quantity' => 12,
        ])->assertCreated();

        $this->assertEquals(18, WardItemStock::where('ward_id', $ward->id)->where('item_id', $item->id)->value('quantity'));
    }

    public function test_it_rejects_an_out_transaction_when_stock_is_insufficient(): void
    {
        $this->actingUser();
        $ward = Ward::factory()->create();
        $item = Item::factory()->create();
        WardItemStock::create(['ward_id' => $ward->id, 'item_id' => $item->id, 'quantity' => 5]);

        $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => $ward->id,
            'item_id' => $item->id,
            'type' => 'out',
            'quantity' => 10,
        ])->assertStatus(422);

        $this->assertEquals(5, WardItemStock::where('ward_id', $ward->id)->where('item_id', $item->id)->value('quantity'));
    }

    public function test_it_lists_transactions_filtered_by_ward(): void
    {
        $this->actingUser();
        $ward = Ward::factory()->create();
        $item = Item::factory()->create();
        $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => $ward->id, 'item_id' => $item->id, 'type' => 'in', 'quantity' => 5,
        ])->assertCreated();
        $this->postJson('/api/v1/ward-stock-transactions', [
            'ward_id' => Ward::factory()->create()->id, 'item_id' => $item->id, 'type' => 'in', 'quantity' => 5,
        ])->assertCreated();

        $response = $this->getJson("/api/v1/ward-stock-transactions?ward_id={$ward->id}");

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_guest_cannot_access_ward_stock_transactions(): void
    {
        $this->getJson('/api/v1/ward-stock-transactions')->assertStatus(401);
    }
}
