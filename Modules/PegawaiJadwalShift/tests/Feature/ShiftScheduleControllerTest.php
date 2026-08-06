<?php

namespace Modules\PegawaiJadwalShift\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralStaffMember\Models\StaffMember;
use Modules\GeneralWard\Models\Ward;
use Modules\PegawaiJadwalShift\Models\ShiftSchedule;
use Tests\TestCase;

class ShiftScheduleControllerTest extends TestCase
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

    public function test_it_creates_shift_schedule_for_a_staff_member(): void
    {
        $this->actingUser();
        $staffMember = StaffMember::factory()->create();
        $ward = Ward::factory()->create();

        $this->postJson('/api/v1/shift-schedules', [
            'staff_member_id' => $staffMember->id,
            'ward_id' => $ward->id,
            'shift_type' => 'pagi',
            'shift_date' => '2026-09-01',
            'start_time' => '07:00',
            'end_time' => '14:00',
        ])
            ->assertCreated()
            ->assertJsonPath('shift_type', 'pagi')
            ->assertJsonPath('status', 'scheduled');
    }

    public function test_it_rejects_duplicate_shift_for_the_same_staff_member_date_and_type(): void
    {
        $this->actingUser();
        $staffMember = StaffMember::factory()->create();

        ShiftSchedule::factory()->create([
            'staff_member_id' => $staffMember->id,
            'employee_id' => null,
            'shift_type' => 'pagi',
            'shift_date' => '2026-09-01',
        ]);

        $this->postJson('/api/v1/shift-schedules', [
            'staff_member_id' => $staffMember->id,
            'shift_type' => 'pagi',
            'shift_date' => '2026-09-01',
            'start_time' => '07:00',
            'end_time' => '14:00',
        ])
            ->assertStatus(422);
    }

    public function test_it_rejects_schedule_with_both_staff_member_and_employee_set(): void
    {
        $this->actingUser();
        $staffMember = StaffMember::factory()->create();
        $employee = Employee::factory()->create();

        $this->postJson('/api/v1/shift-schedules', [
            'staff_member_id' => $staffMember->id,
            'employee_id' => $employee->id,
            'shift_type' => 'siang',
            'shift_date' => '2026-09-02',
            'start_time' => '14:00',
            'end_time' => '21:00',
        ])
            ->assertStatus(422);
    }

    public function test_it_rejects_schedule_with_neither_staff_member_nor_employee_set(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/shift-schedules', [
            'shift_type' => 'malam',
            'shift_date' => '2026-09-02',
            'start_time' => '21:00',
            'end_time' => '07:00',
        ])
            ->assertStatus(422);
    }

    public function test_it_lists_who_is_on_duty_by_ward_and_date_range(): void
    {
        $this->actingUser();
        $ward = Ward::factory()->create();
        $otherWard = Ward::factory()->create();

        ShiftSchedule::factory()->create([
            'ward_id' => $ward->id,
            'shift_date' => '2026-09-05',
            'shift_type' => 'pagi',
        ]);
        ShiftSchedule::factory()->create([
            'ward_id' => $ward->id,
            'shift_date' => '2026-09-10',
            'shift_type' => 'siang',
        ]);
        // di luar rentang tanggal
        ShiftSchedule::factory()->create([
            'ward_id' => $ward->id,
            'shift_date' => '2026-09-20',
            'shift_type' => 'malam',
        ]);
        // ward lain
        ShiftSchedule::factory()->create([
            'ward_id' => $otherWard->id,
            'shift_date' => '2026-09-06',
            'shift_type' => 'pagi',
        ]);

        $this->getJson('/api/v1/shift-schedules-by-ward?ward_id='.$ward->id.'&from=2026-09-01&to=2026-09-15')
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_it_lists_shift_schedules(): void
    {
        $this->actingUser();
        ShiftSchedule::factory()->count(3)->create();

        $this->getJson('/api/v1/shift-schedules')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }
}
