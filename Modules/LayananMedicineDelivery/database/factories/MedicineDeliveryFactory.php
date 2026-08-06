<?php

namespace Modules\LayananMedicineDelivery\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\LayananMedicineDelivery\Models\MedicineDelivery;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;

class MedicineDeliveryFactory extends Factory
{
    protected $model = MedicineDelivery::class;

    public function definition(): array
    {
        return [
            'pharmacy_dispense_id' => PharmacyDispense::factory(),
            'patient_address' => fake()->address(),
            'courier_employee_id' => null,
            'status' => MedicineDelivery::STATUS_PENDING,
            'requested_at' => now()->format('Y-m-d H:i:s'),
            'delivered_at' => null,
        ];
    }
}
