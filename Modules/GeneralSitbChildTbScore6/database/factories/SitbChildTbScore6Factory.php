<?php

namespace Modules\GeneralSitbChildTbScore6\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbChildTbScore6\Models\SitbChildTbScore6;

class SitbChildTbScore6Factory extends Factory
{
    protected $model = SitbChildTbScore6::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}