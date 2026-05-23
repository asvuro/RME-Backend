<?php

namespace Modules\GeneralSitbTb03RoTransfer\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbTb03RoTransfer\Models\SitbTb03RoTransfer;

class SitbTb03RoTransferFactory extends Factory
{
    protected $model = SitbTb03RoTransfer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}