<?php

namespace Modules\MedicalRecordRetentionSchedule\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPatient\Models\Patient;
use Modules\MedicalRecordRetentionSchedule\Models\RetentionSchedule;
use Modules\PendaftaranRegistration\Models\Registration;

class RetentionScheduleFactory extends Factory
{
    protected $model = RetentionSchedule::class;

    public function definition(): array
    {
        $basisDate = now()->subYears(5);
        $years = 25;

        return [
            'registration_id' => Registration::factory(),
            'patient_id' => Patient::factory(),
            'basis_date' => $basisDate,
            'retention_years' => $years,
            'retention_due_at' => $basisDate->copy()->addYears($years)->toDateString(),
            'status' => RetentionSchedule::STATUS_ACTIVE,
            'marked_by' => null,
            'marked_at' => null,
            'notes' => null,
        ];
    }

    public function eligibleForDestruction(): static
    {
        return $this->state(function () {
            $basisDate = now()->subYears(30);
            $years = 25;

            return [
                'basis_date' => $basisDate,
                'retention_years' => $years,
                'retention_due_at' => $basisDate->copy()->addYears($years)->toDateString(),
                'status' => RetentionSchedule::STATUS_ELIGIBLE_FOR_DESTRUCTION,
            ];
        });
    }
}
