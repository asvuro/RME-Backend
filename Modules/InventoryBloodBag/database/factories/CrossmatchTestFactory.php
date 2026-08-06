<?php

namespace Modules\InventoryBloodBag\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPatient\Models\Patient;
use Modules\InventoryBloodBag\Models\BloodBag;
use Modules\InventoryBloodBag\Models\CrossmatchTest;

class CrossmatchTestFactory extends Factory
{
    protected $model = CrossmatchTest::class;

    public function definition(): array
    {
        $testedAt = now();

        return [
            'blood_bag_id' => BloodBag::factory(),
            'patient_id' => Patient::factory(),
            'major_result' => CrossmatchTest::RESULT_NEGATIVE,
            'minor_result' => CrossmatchTest::RESULT_NEGATIVE,
            'auto_control' => CrossmatchTest::RESULT_NEGATIVE,
            'is_compatible' => true,
            'tested_by' => null,
            'tested_at' => $testedAt,
            'reserved_until' => $testedAt->copy()->addHours(48),
        ];
    }
}
