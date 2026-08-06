<?php

namespace Modules\InventorySterilizationCycle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\InventorySterilizationCycle\Models\SterilizationCycle;

class SterilizationCycleFactory extends Factory
{
    protected $model = SterilizationCycle::class;

    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 month', 'now');

        return [
            // generateCycleNumber() baca max lalu +1 -- di bulk factory
            // (count()->create()) semua instance dibangun sebelum satu pun
            // tersimpan, jadi semuanya membaca count yang sama dan bentrok.
            // Factory pakai nomor unik acak, bukan generator produksi.
            'cycle_number' => sprintf('CYC-%s-%06d', now()->format('Y'), fake()->unique()->numberBetween(1, 999999)),
            'machine_name' => 'Autoklaf '.fake()->numberBetween(1, 5),
            'temperature_celsius' => fake()->randomFloat(2, 121, 134),
            'pressure_bar' => fake()->randomFloat(2, 1, 3),
            'duration_minutes' => fake()->numberBetween(15, 60),
            'started_at' => $startedAt,
            'completed_at' => null,
            'biological_indicator_result' => SterilizationCycle::BI_PENDING,
            'status' => SterilizationCycle::STATUS_IN_PROCESS,
        ];
    }

    /** State: siklus lulus dan siap dipakai untuk membuat SterilizedItem. */
    public function passed(): static
    {
        return $this->state(fn () => [
            'completed_at' => now(),
            'biological_indicator_result' => SterilizationCycle::BI_NEGATIVE,
            'status' => SterilizationCycle::STATUS_PASSED,
        ]);
    }
}
