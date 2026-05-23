<?php

namespace Modules\GeneralPatientType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPatientType\Database\Factories\PatientTypeFactory;

class PatientType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PatientTypeFactory
    {
        return PatientTypeFactory::new();
    }
}