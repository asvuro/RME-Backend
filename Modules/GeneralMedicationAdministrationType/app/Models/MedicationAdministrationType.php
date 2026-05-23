<?php

namespace Modules\GeneralMedicationAdministrationType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralMedicationAdministrationType\Database\Factories\MedicationAdministrationTypeFactory;

class MedicationAdministrationType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): MedicationAdministrationTypeFactory
    {
        return MedicationAdministrationTypeFactory::new();
    }
}