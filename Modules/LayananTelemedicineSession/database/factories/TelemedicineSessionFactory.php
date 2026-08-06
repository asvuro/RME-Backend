<?php

namespace Modules\LayananTelemedicineSession\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananTelemedicineSession\Models\TelemedicineSession;
use Modules\PendaftaranVisit\Models\Visit;

class TelemedicineSessionFactory extends Factory
{
    protected $model = TelemedicineSession::class;

    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'doctor_employee_id' => Employee::factory(),
            'scheduled_at' => now()->addDay(),
            'started_at' => null,
            'ended_at' => null,
            'session_url' => null,
            'status' => TelemedicineSession::STATUS_SCHEDULED,
            'consultation_notes' => null,
        ];
    }

    public function ongoing(): static
    {
        return $this->state(fn () => [
            'status' => TelemedicineSession::STATUS_ONGOING,
            'started_at' => now()->subMinutes(15),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => TelemedicineSession::STATUS_COMPLETED,
            'started_at' => now()->subHour(),
            'ended_at' => now()->subMinutes(5),
        ]);
    }
}
