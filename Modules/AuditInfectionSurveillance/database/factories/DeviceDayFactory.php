<?php

namespace Modules\AuditInfectionSurveillance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AuditInfectionSurveillance\Models\DeviceDay;
use Modules\PendaftaranVisit\Models\Visit;

class DeviceDayFactory extends Factory
{
    protected $model = DeviceDay::class;

    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'device_type' => fake()->randomElement(DeviceDay::TYPES),
            'inserted_at' => now()->subDays(3),
            'removed_at' => null,
        ];
    }

    /** Alat yang sudah dilepas: pasang 3 hari lalu, lepas kemarin. */
    public function removed(): static
    {
        return $this->state(fn () => [
            'removed_at' => now()->subDay(),
        ]);
    }
}
