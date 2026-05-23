<?php

namespace Modules\GeneralSitbDiagnosisType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbDiagnosisType\Models\SitbDiagnosisType;

class SitbDiagnosisTypeFactory extends Factory
{
    protected $model = SitbDiagnosisType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}