<?php

namespace Modules\GeneralBed\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralRoom\Models\Room;
use Modules\GeneralStaffMember\Models\StaffMember;
use Modules\GeneralStaffWardAssignment\Models\StaffWardAssignment;
use Tests\TestCase;

class BedControllerTest extends TestCase
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

    public function test_it_creates_bed_under_a_room(): void
    {
        $this->actingUser();
        $room = Room::factory()->create();

        $this->postJson('/api/v1/beds', ['room_id' => $room->id, 'bed_number' => 'B-01'])
            ->assertCreated()
            ->assertJsonPath('bed_number', 'B-01');
    }

    public function test_it_lists_beds_filtered_by_room(): void
    {
        $this->actingUser();
        $room = Room::factory()->create();
        Bed::factory()->count(2)->create(['room_id' => $room->id]);
        Bed::factory()->create();

        $this->getJson("/api/v1/beds?room_id={$room->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_ward_staff_cannot_create_bed_in_another_ward(): void
    {
        $ownWard = \Modules\GeneralWard\Models\Ward::factory()->create();
        $otherWard = \Modules\GeneralWard\Models\Ward::factory()->create();
        $this->actingWardStaff($ownWard->id);
        $room = Room::factory()->create(['ward_id' => $otherWard->id]);

        $this->postJson('/api/v1/beds', ['room_id' => $room->id, 'bed_number' => 'B-01'])
            ->assertStatus(403);
    }

    public function test_ward_staff_can_create_bed_in_own_ward(): void
    {
        $ownWard = \Modules\GeneralWard\Models\Ward::factory()->create();
        $this->actingWardStaff($ownWard->id);
        $room = Room::factory()->create(['ward_id' => $ownWard->id]);

        $this->postJson('/api/v1/beds', ['room_id' => $room->id, 'bed_number' => 'B-01'])
            ->assertCreated();
    }

    public function test_ward_staff_cannot_update_bed_in_another_ward(): void
    {
        $ownWard = \Modules\GeneralWard\Models\Ward::factory()->create();
        $otherWard = \Modules\GeneralWard\Models\Ward::factory()->create();
        $this->actingWardStaff($ownWard->id);
        $room = Room::factory()->create(['ward_id' => $otherWard->id]);
        $bed = Bed::factory()->create(['room_id' => $room->id]);

        $this->putJson("/api/v1/beds/{$bed->id}", ['bed_number' => 'B-99'])
            ->assertStatus(403);
    }

    public function test_ward_staff_cannot_delete_bed_in_another_ward(): void
    {
        $ownWard = \Modules\GeneralWard\Models\Ward::factory()->create();
        $otherWard = \Modules\GeneralWard\Models\Ward::factory()->create();
        $this->actingWardStaff($ownWard->id);
        $room = Room::factory()->create(['ward_id' => $otherWard->id]);
        $bed = Bed::factory()->create(['room_id' => $room->id]);

        $this->deleteJson("/api/v1/beds/{$bed->id}")
            ->assertStatus(403);
    }
}
