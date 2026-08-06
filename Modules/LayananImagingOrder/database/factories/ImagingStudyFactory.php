<?php

namespace Modules\LayananImagingOrder\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\LayananImagingOrder\Models\ImagingOrder;
use Modules\LayananImagingOrder\Models\ImagingStudy;

class ImagingStudyFactory extends Factory
{
    protected $model = ImagingStudy::class;

    public function definition(): array
    {
        return [
            'imaging_order_id' => ImagingOrder::factory(),
            'study_instance_uid' => fake()->unique()->uuid(),
            'performed_at' => now(),
            'findings_summary' => fake()->paragraph(),
            'report_url' => null,
        ];
    }
}
