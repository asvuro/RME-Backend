<?php

namespace Modules\GeneralMedicationUsageType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralMedicationUsageType\Database\Factories\MedicationUsageTypeFactory;

class MedicationUsageType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): MedicationUsageTypeFactory
    {
        return MedicationUsageTypeFactory::new();
    }
}