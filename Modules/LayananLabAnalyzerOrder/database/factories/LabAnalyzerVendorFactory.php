<?php

namespace Modules\LayananLabAnalyzerOrder\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\LayananLabAnalyzerOrder\Models\LabAnalyzerVendor;

class LabAnalyzerVendorFactory extends Factory
{
    protected $model = LabAnalyzerVendor::class;

    public function definition(): array
    {
        return [
            'vendor_name' => fake()->unique()->randomElement(['Novanet', 'Vanslab', 'Winacom']).'-'.fake()->unique()->numerify('###'),
            'connection_notes' => fake()->optional()->sentence(),
        ];
    }
}
