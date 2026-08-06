<?php

namespace Modules\PendaftaranSelfCheckin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPatient\Models\Patient;
use Modules\PendaftaranSelfCheckin\Models\SelfCheckinQueue;

class SelfCheckinQueueFactory extends Factory
{
    protected $model = SelfCheckinQueue::class;

    public function definition(): array
    {
        $checkedInAt = now()->subMinutes($this->faker->numberBetween(0, 120));

        return [
            'patient_id' => Patient::factory(),
            'nik' => null,
            // Composite-unique scope is (ward_id, queue_date, queue_number);
            // faker uniqueness keeps factory-made rows collision-free enough
            // for tests that do not care about exact numbers.
            'queue_number' => sprintf('%03d', $this->faker->unique()->numberBetween(1, 999)),
            'ward_id' => null,
            'queue_date' => $checkedInAt->format('Y-m-d'),
            'checked_in_at' => $checkedInAt,
            'status' => SelfCheckinQueue::STATUS_WAITING,
            'called_at' => null,
            'called_by' => null,
        ];
    }

    public function called(): static
    {
        return $this->state(fn () => [
            'status' => SelfCheckinQueue::STATUS_CALLED,
            'called_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => SelfCheckinQueue::STATUS_COMPLETED,
            'called_at' => now(),
        ]);
    }
}
