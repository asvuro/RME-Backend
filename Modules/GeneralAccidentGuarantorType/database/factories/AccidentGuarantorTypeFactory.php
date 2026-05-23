<?php

namespace Modules\GeneralAccidentGuarantorType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralAccidentGuarantorType\Models\AccidentGuarantorType;

class AccidentGuarantorTypeFactory extends Factory
{
    protected $model = AccidentGuarantorType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}