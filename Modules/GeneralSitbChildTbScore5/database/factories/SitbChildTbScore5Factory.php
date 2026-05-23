<?php

namespace Modules\GeneralSitbChildTbScore5\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbChildTbScore5\Models\SitbChildTbScore5;

class SitbChildTbScore5Factory extends Factory
{
    protected $model = SitbChildTbScore5::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}