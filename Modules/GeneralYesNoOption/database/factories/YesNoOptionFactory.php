<?php

namespace Modules\GeneralYesNoOption\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralYesNoOption\Models\YesNoOption;

class YesNoOptionFactory extends Factory
{
    protected $model = YesNoOption::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}