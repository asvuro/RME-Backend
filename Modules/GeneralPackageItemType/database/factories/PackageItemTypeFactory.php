<?php

namespace Modules\GeneralPackageItemType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPackageItemType\Models\PackageItemType;

class PackageItemTypeFactory extends Factory
{
    protected $model = PackageItemType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}