<?php

namespace Modules\PegawaiRemunerasiJasaMedis\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmployee\Models\Employee;
use Modules\PegawaiRemunerasiJasaMedis\Models\RemunerationEntry;

class RemunerationEntryFactory extends Factory
{
    protected $model = RemunerationEntry::class;

    public function definition(): array
    {
        $gross = fake()->randomFloat(2, 100000, 1000000);
        $deductionPercentage = fake()->randomElement([0, 5, 10]);
        $fixedDeduction = 0;
        $net = round($gross - ($gross * $deductionPercentage / 100) - $fixedDeduction, 2);

        return [
            'employee_id' => Employee::factory(),
            'source_type' => 'tindakan',
            'source_id' => fake()->numberBetween(1, 1000),
            'role' => RemunerationEntry::ROLE_OPERATOR_UTAMA,
            'gross_amount' => $gross,
            'deduction_percentage' => $deductionPercentage,
            'fixed_deduction' => $fixedDeduction,
            'net_amount' => $net,
            'service_date' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'notes' => null,
        ];
    }
}
