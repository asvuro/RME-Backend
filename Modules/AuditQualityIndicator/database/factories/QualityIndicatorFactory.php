<?php

namespace Modules\AuditQualityIndicator\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AuditQualityIndicator\Models\QualityIndicator;

class QualityIndicatorFactory extends Factory
{
    protected $model = QualityIndicator::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('INM-##-###'),
            'name' => fake()->words(3, true),
            'unit_of_measure' => '%',
            'target_value' => fake()->randomFloat(2, 50, 100),
            'category' => fake()->randomElement(QualityIndicator::CATEGORIES),
        ];
    }
}
