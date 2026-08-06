<?php

namespace Modules\PegawaiJadwalShift\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralStaffMember\Models\StaffMember;
use Modules\PegawaiJadwalShift\Models\ShiftSchedule;

class ShiftScheduleFactory extends Factory
{
    protected $model = ShiftSchedule::class;

    public function definition(): array
    {
        return [
            'staff_member_id' => StaffMember::factory(),
            'employee_id' => null,
            'ward_id' => null,
            'shift_type' => fake()->randomElement(['pagi', 'siang', 'malam']),
            'shift_date' => fake()->date(),
            'start_time' => '07:00:00',
            'end_time' => '14:00:00',
            'status' => 'scheduled',
        ];
    }
}
