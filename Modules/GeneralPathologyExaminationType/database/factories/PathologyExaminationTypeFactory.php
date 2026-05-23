<?php

namespace Modules\GeneralPathologyExaminationType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPathologyExaminationType\Models\PathologyExaminationType;

class PathologyExaminationTypeFactory extends Factory
{
    protected $model = PathologyExaminationType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}