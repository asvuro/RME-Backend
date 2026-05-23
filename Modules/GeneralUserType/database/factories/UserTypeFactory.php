<?php

namespace Modules\GeneralUserType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralUserType\Models\UserType;

class UserTypeFactory extends Factory
{
    protected $model = UserType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}