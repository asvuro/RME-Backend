<?php

namespace Modules\AuditInfectionSurveillance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AuditInfectionSurveillance\Models\InfectionCase;
use Modules\PendaftaranVisit\Models\Visit;

class InfectionCaseFactory extends Factory
{
    protected $model = InfectionCase::class;

    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'infection_type' => fake()->randomElement(InfectionCase::TYPES),
            'diagnosed_at' => now()->subDay(),
            'related_device_day_id' => null,
        ];
    }
}
