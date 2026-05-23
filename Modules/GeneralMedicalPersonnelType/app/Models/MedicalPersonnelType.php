<?php

namespace Modules\GeneralMedicalPersonnelType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralMedicalPersonnelType\Database\Factories\MedicalPersonnelTypeFactory;

class MedicalPersonnelType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): MedicalPersonnelTypeFactory
    {
        return MedicalPersonnelTypeFactory::new();
    }
}