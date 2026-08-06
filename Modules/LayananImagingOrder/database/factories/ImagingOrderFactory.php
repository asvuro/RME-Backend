<?php

namespace Modules\LayananImagingOrder\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananImagingOrder\Models\ImagingOrder;
use Modules\PendaftaranVisit\Models\Visit;

class ImagingOrderFactory extends Factory
{
    protected $model = ImagingOrder::class;

    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'modality' => fake()->randomElement(ImagingOrder::MODALITIES),
            'body_part' => fake()->randomElement(['Thorax', 'Abdomen', 'Kepala', 'Extremities']),
            'ordered_by' => Employee::factory(),
            'ordered_at' => now(),
            'scheduled_at' => null,
            'status' => ImagingOrder::STATUS_ORDERED,
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => ImagingOrder::STATUS_SCHEDULED,
            'scheduled_at' => now()->addDay(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => ImagingOrder::STATUS_COMPLETED,
            'scheduled_at' => now()->subDay(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => ImagingOrder::STATUS_CANCELLED,
        ]);
    }
}
