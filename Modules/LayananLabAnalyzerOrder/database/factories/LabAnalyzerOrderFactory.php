<?php

namespace Modules\LayananLabAnalyzerOrder\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananLabAnalyzerOrder\Models\LabAnalyzerOrder;
use Modules\LayananLabAnalyzerOrder\Models\LabAnalyzerVendor;
use Modules\PendaftaranVisit\Models\Visit;

class LabAnalyzerOrderFactory extends Factory
{
    protected $model = LabAnalyzerOrder::class;

    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'vendor_id' => LabAnalyzerVendor::factory(),
            'test_code' => fake()->randomElement(['HBA1C', 'CBC', 'LFT', 'RFT', 'LIPID']),
            'ordered_by' => Employee::factory(),
            'ordered_at' => now(),
            'status' => LabAnalyzerOrder::STATUS_ORDERED,
            'raw_result_text' => null,
            'verified_by' => null,
            'verified_at' => null,
        ];
    }

    /**
     * State lanjutan untuk test gerbang: hasil sudah masuk dari analyzer.
     */
    public function resultReceived(): static
    {
        return $this->state(fn () => [
            'status' => LabAnalyzerOrder::STATUS_RESULT_RECEIVED,
            'raw_result_text' => '<OBX>|||HBA1C|5.9|%</OBX>',
        ]);
    }
}
