<?php

namespace Modules\LayananMortuaryRecord\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralPatient\Models\Patient;
use Modules\LayananMortuaryRecord\Models\MortuaryRecord;

class MortuaryRecordFactory extends Factory
{
    protected $model = MortuaryRecord::class;

    public function definition(): array
    {
        return [
            'visit_id' => null,
            'patient_id' => Patient::factory(),
            'admitted_at' => fake()->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'),
            'released_at' => null,
            'cause_of_death_notes' => fake()->sentence(),
            'released_to_name' => null,
            'released_to_relationship' => null,
            'released_by' => null,
            'status' => MortuaryRecord::STATUS_IN_MORTUARY,
        ];
    }

    public function released(): self
    {
        return $this->state(fn () => [
            'released_at' => now(),
            'released_to_name' => fake()->name(),
            'released_to_relationship' => 'Anak Kandung',
            // Record released tanpa jejak petugas tidak boleh ada (gerbang #2).
            'released_by' => Employee::factory(),
            'status' => MortuaryRecord::STATUS_RELEASED,
        ]);
    }
}
