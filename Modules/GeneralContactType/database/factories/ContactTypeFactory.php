<?php

namespace Modules\GeneralContactType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralContactType\Models\ContactType;

class ContactTypeFactory extends Factory
{
    protected $model = ContactType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}