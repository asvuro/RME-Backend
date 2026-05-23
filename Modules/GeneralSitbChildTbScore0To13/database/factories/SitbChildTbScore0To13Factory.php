<?php

namespace Modules\GeneralSitbChildTbScore0To13\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbChildTbScore0To13\Models\SitbChildTbScore0To13;

class SitbChildTbScore0To13Factory extends Factory
{
    protected $model = SitbChildTbScore0To13::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}